<?php

namespace App\Functions;

use App\Models\Invoice;
use Exception;
use Illuminate\Support\Facades\Log;

class Fcm
{
    static function send($data, $token)
    {
        try {
            $payload = [
                'to' => $token,
                'notification' => [
                    'title' => $data['title'],
                    'body' => $data['body'],
                    'click_action' => 'ORDER_ACTION',
                ],
                'data' => [
                    'order_number' => $data['order_number'],
                    'order_id' => $data['order_id'],
                ]
            ];

            $headers = array(
                'Authorization: key=' . env('FCM_SERVER_KEY'),
                'Content-Type: application/json'
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            $result = curl_exec($ch);
            curl_close($ch);
        } catch (Exception $err) {
            return [
                'status' => false,
                'message' => $err->getMessage()
            ];
        }
    }

    static function sendAction($data, $action = "ADS_MESSAGE")
    {
        $payload = [
            'to' => $data['token'],
            'notification' => [
                'title' => $data['title'],
                'body' => $data['body'],
                'click_action' => $action,
            ]
        ];

        $headers = array(
            'Authorization: key=' . env('FCM_SERVER_KEY'),
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        $result = curl_exec($ch);
        Log::info("Fcm", [json_decode($result)]);
        curl_close($ch);
    }

    static function sendAll($data, $topic = 'all')
    {
        try {
            $data = [
                'to' => '/topics/' . $topic,
                'notification' => [
                    'title' => $data['title'],
                    'body' => $data['body'],
                    'click_action' => 'ADS_MESSAGE',
                ],
                'data' => [
                    'invoice' => '',
                ]
            ];

            $headers = array(
                'Authorization: key=' . env('FCM_SERVER_KEY'),
                'Content-Type: application/json'
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
        } catch (Exception $err) {
            return [
                'status' => false,
                'message' => $err->getMessage()
            ];
        }
    }

    static function sendAllAction($data, $topic = 'all', $actionType = null, $actionId = null, $image = null)
    {
        try {

            if ($image) {
                $data = [
                    'to' => '/topics/' . $topic,
                    'notification' => [
                        'title' => $data['title'],
                        'body' => $data['body'],
                        'image' => $image,
                    ],
                    'android' => [
                        'notification' => [
                            'image' => $image,
                        ]
                    ],
                    'apns' => [
                        "payload" => [
                            "aps" => [
                                "mutable-content" => 1,
                                "alert" => [
                                    "title" => $data['title'],
                                    "body" => $data['body']
                                ],
                                "category" => "ADS_MESSAGE",
                                "sound" => "default"
                            ]
                        ],
                        "fcm_options" => [
                            "image" => $image
                        ]
                    ],
                    'data' => [
                        'type' => $actionType,
                        'id' => $actionId,
                        'image' => $image,
                    ]
                ];
            } else {
                $data = [
                    'to' => '/topics/' . $topic,
                    'notification' => [
                        'title' => $data['title'],
                        'body' => $data['body']
                    ],
                    'data' => [
                        'type' => $actionType,
                        'id' => $actionId,
                    ]
                ];
            }

            $headers = array(
                'Authorization: key=' . env('FCM_SERVER_KEY'),
                'Content-Type: application/json'
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $result = curl_exec($ch);
            Log::info('Send FCM [' . $topic . '] ' . $result);
            curl_close($ch);
            return $result;
        } catch (Exception $err) {
            return [
                'status' => false,
                'message' => $err->getMessage()
            ];
        }
    }

    static function subscribe($token, $topic = 'all')
    {
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://iid.googleapis.com/iid/v1/' . $token . '/rel/topics/' . $topic,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: key=' . env('FCM_SERVER_KEY'),
                    'Content-Length: 0',
                ),
            ));

            $result = curl_exec($curl);
            Log::info('subscribe to topic: [' . $topic . '] ' . $result);
            curl_close($curl);
            // log the result
        } catch (Exception $err) {
            return [
                'status' => false,
                'message' => $err->getMessage()
            ];
        }
    }

    static function unsubscribe($token, $topic = 'all')
    {
        try {
            $data = [
                "to" => "/topics/{$topic}",
                "registration_tokens" =>  [$token],
            ];

            $headers = array(
                'Authorization: key=' . env('FCM_SERVER_KEY'),
                'Content-Type: application/json'
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://iid.googleapis.com/iid/v1:batchRemove');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $result = curl_exec($ch);
            Log::info("Unsubscribe FCM from topic: {$topic}" . $result);
            curl_close($ch);
            return $result;
        } catch (Exception $err) {
            return [
                'status' => false,
                'message' => $err->getMessage()
            ];
        }
    }
}
