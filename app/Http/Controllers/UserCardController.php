<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Maherelgamil\LaravelFawry\Fawry;
use Validator;

class UserCardController extends Controller
{
    private $fawry;

    public function __construct()
    {
        $this->fawry = new Fawry();
    }
    /**
     * return a listing of user credit cards.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = $this->fawry->listCustomerTokens(auth()->user());
        $cards = $result->cards;
        return response()->json([
            'message' => __('messages.r_success'),
            'data' => $cards,
            'error' => null,
        ], 200);
    }

    /**
     * Add new card.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        // validate credit card request
        $validator = $this->validateCardRequest($request);
        if ($validator->fails()) {
            return response()->json([
                'message' => __('messages.add_card_error'),
                'data' => null,
                'error' => $validator->errors(),
            ], 422);
        }

        try {
            $tokenResponse = $this->fawry->createCardToken(request('card_number'), request('expiry_year'), request('expiry_month'), request('cvv'), auth()->user());
            if ($tokenResponse->statusCode == 200) {
                return response()->json([
                    'message' => __('messages.add_card_success'),
                    'data' => $tokenResponse->card,
                    'error' => null,
                ], 200);
            } else {
                throw new \Exception($tokenResponse->statusDescription);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('messages.add_card_error'),
                'data' => null,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $token
     * @return \Illuminate\Http\Response
     */
    public function delete($token)
    {
        try {
            $result = $this->fawry->deleteCardToken(auth()->user(), $token);
            if ($result->statusCode == 200) {
                return response()->json([
                    'message' => __('messages.delete_card_success'),
                    'data' => null,
                    'error' => null,
                ], 200);
            } else {
                throw new \Exception($result->statusDescription);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('messages.delete_card_error'),
                'data' => null,
                'error' => $e->getMessage(),
            ], 400);
        }
    }


    private function validateCardRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'card_number' => 'required|digits_between:13,16',
            'expiry_year' => 'required|digits:2|integer',
            'expiry_month' => 'required',
            'cvv' => 'required|digits_between:3,4',
        ]);

        $validator->after(function ($validator) use ($request) {
            $invalidMonth = false;
            if ((int) $request->expiry_month > 12 || (int) $request->expiry_month < 1) {
                $validator->errors()->add('expiry_month', __('messages.invalid_expiry_month'));
                $invalidMonth = true;
            }
            if (!$invalidMonth) {
                // check if card will expire in next 30 days
                $expiry_month = $request->expiry_month;
                $expiry_year = $request->expiry_year;
                $expiry_date = Carbon::createFromDate('20' . $expiry_year, $expiry_month, 1);
                $now = Carbon::now();
                $diff = $expiry_date->diffInDays($now);
                if ($diff < 30) {
                    $validator->errors()->add('expiry_year', __('messages.card_expired'));
                }
            }
        });

        return $validator;
    }
}
