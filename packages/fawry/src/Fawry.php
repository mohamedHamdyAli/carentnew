<?php

namespace Maherelgamil\LaravelFawry;

class Fawry
{
    public $merchantCode;

    public $securityKey;

    public function __construct()
    {
        $this->merchantCode = config('fawry.merchant_code');

        $this->securityKey = config('fawry.security_key');
    }

    public function endpoint($uri)
    {
        return config('fawry.debug') ?
            'https://atfawry.fawrystaging.com/ECommerceWeb/Fawry/' . $uri :
            'https://www.atfawry.com/ECommerceWeb/Fawry/' . $uri;
    }

    public function createCardToken($cardNumber, $expiryYear, $expiryMonth, $cvv, $user)
    {
        $result =  $this->post(
            $this->endpoint("cards/cardToken"),
            [
                "merchantCode" => $this->merchantCode,
                "customerProfileId" => md5($user->id),
                "customerMobile" => $user->mobile,
                "customerEmail" => $user->email,
                "cardNumber" => $cardNumber,
                "expiryYear" => $expiryYear,
                "expiryMonth" => $expiryMonth,
                "cvv" => $cvv,
                "isDefault" => true,
            ]
        );

        // $brands = [
        //     4 => 'VisaCard',
        //     5 => 'MasterCard',
        // ];

        // if ($result->statusCode == 200) {

        //     $user->update([
        //         'payment_card_last_four' => $result->card->lastFourDigits,
        //         'payment_card_brand' => $brands[substr($result->card->firstSixDigits, 0, 1)],
        //         'payment_card_fawry_token' => $result->card->token,
        //     ]);
        // }

        return $result;
    }

    public function listCustomerTokens($user)
    {
        return $this->get(
            $this->endpoint("cards/cardToken"),
            [
                'merchantCode' => $this->merchantCode,
                'customerProfileId' => md5($user->id),
                'signature' => hash('sha256', $this->merchantCode . md5($user->id) . $this->securityKey),
            ]
        );
    }

    public function deleteCardToken($user, $token = null)
    {
        $iToken = $token ? $token : $user->payment_card_fawry_token;
        $signature = hash('sha256' , $this->merchantCode . md5($user->id) . $iToken . $this->securityKey);
        $result =  $this->delete(
            $this->endpoint("cards/cardToken"),
            [
                'merchantCode' => $this->merchantCode,
                'customerProfileId' => md5($user->id),
                'cardToken' => $iToken,
                'signature' => $signature,
            ]
        );

        // if ($result->statusCode == 200) {
        //     $user->update([
        //         'payment_card_last_four' => null,
        //         'payment_card_brand' => null,
        //         'payment_card_fawry_token' => null,
        //     ]);
        // }

        return $result;
    }

    public function chargeViaCard($merchantRefNum, $user, $amount, $chargeItems = [], $description = null)
    {
        return $this->post(
            $this->endpoint("cards/cardToken"),
            [
                'merchantCode' => $this->merchantCode,
                'merchantRefNum' => $merchantRefNum,
                'paymentMethod' => 'CARD',
                'cardToken' => $user->payment_card_fawry_token,
                'customerProfileId' => md5($user->id),
                'customerMobile' => $user->mobile ?? $user->phone,
                'customerEmail' => $user->email,
                'amount' => $amount,
                'currencyCode' => 'EGP',
                'chargeItems' => $chargeItems,
                'description' => $description,
                'signature' => hash(
                    'sha256',
                    $this->merchantCode .
                        $merchantRefNum .
                        md5($user->id) .
                        'CARD' .
                        (float) $amount .
                        $user->payment_card_fawry_token .
                        $this->securityKey
                )
            ]
        );
    }

    public function chargeViaFawry($merchantRefNum, $user, $paymentExpiry, $amount, $chargeItems = [], $description = null)
    {
        return $this->post(
            $this->endpoint("payments/charge"),
            [
                [
                    'merchantCode' => $this->merchantCode,
                    'merchantRefNum' => $merchantRefNum,
                    'paymentMethod' => 'PAYATFAWRY',
                    'paymentExpiry' => $paymentExpiry,
                    'customerProfileId' => md5($user->id),
                    'customerMobile' => $user->mobile ?? $user->phone,
                    'customerEmail' => $user->email,
                    'amount' => $amount,
                    'currencyCode' => 'EGP',
                    'chargeItems' => $chargeItems,
                    'description' => $description,
                    'signature' => hash(
                        'sha256',
                        $this->merchantCode .
                            $merchantRefNum .
                            md5($user->id) .
                            'PAYATFAWRY' .
                            (float) $amount .
                            $this->securityKey
                    )
                ]
            ]
        );
    }

    public function refund($fawryRefNumber, $refundAmount, $reason = null)
    {
        return $this->post(
            $this->endpoint("payments/refund"),
            [
                'merchantCode' => $this->merchantCode,
                'referenceNumber' => $fawryRefNumber,
                'refundAmount' => $refundAmount,
                'reason' => $reason,
                'signature' => hash(
                    'sha256',
                    $this->merchantCode .
                        $fawryRefNumber .
                        number_format((float) $refundAmount, 2) .
                        $this->securityKey
                )
            ]
        );
    }

    public function get($url, $data)
    {
        $params = '';
        foreach ($data as $key => $value)
            $params .= $key . '=' . $value . '&';

        $params = trim($params, '&');

        $ch = curl_init($url . "?" . $params);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        return json_decode(curl_exec($ch));
    }

    public function post($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen(json_encode($data))
            )
        );

        return json_decode(curl_exec($ch));
    }

    public function delete($url, $data)
    {
        $params = '';
        foreach ($data as $key => $value)
            $params .= $key . '=' . $value . '&';

        $params = trim($params, '&');

        $ch = curl_init($url . "?" . $params);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        return json_decode(curl_exec($ch));
    }
}
