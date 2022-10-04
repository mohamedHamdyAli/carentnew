<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendNotificationRequest;
use App\Jobs\SendPushNotification;
use App\Jobs\SendPushNotificationToUser;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function send(SendNotificationRequest $request)
    {
        $topic = $request->target;

        if ($topic == 'user') {
            $user = User::where('email', $request->email)->firstOrFail();
            $token = $user->fcm;
            if ($token != null) {
                SendPushNotificationToUser::dispatch([
                    'token' => $token,
                    'title' => $request->title,
                    'body' => $request->message,
                ]);
            } else {
                return response()->json([
                    'message' => 'User does not have a token'
                ], Response::HTTP_BAD_REQUEST);
            }
        } else {
            $lang = '';

            // country code
            if ($request->has('country') && $request->country) {
                $lang = $request->country . '-';
            }

            // topic
            if ($topic != 'all') {
                $topic = 'all-' . $lang . $request->target;
            }

            // set the notification language
            if ($request->has('language') && $request->language) {
                $topic .= '-' . $request->language;
            }

            // send the notification
            SendPushNotification::dispatch([
                'title' => $request->title,
                'body' => $request->message,
                'topic' => $topic
            ]);
        }

        return response(true, Response::HTTP_OK);
    }
}
