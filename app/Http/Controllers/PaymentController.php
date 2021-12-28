<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Order;
use Carbon\Carbon;
use Hash;
use Illuminate\Http\Request;
use Maherelgamil\LaravelFawry\Fawry;

class PaymentController extends Controller
{
    public function pay()
    {
        $this->validate(request(), [
            'cardToken' => 'required',
            'order_id' => 'required|string|exists:orders,id',
            'cvv' => 'required|string|size:3',
        ]);

        // get order
        $order = Order::findOrFail(request('order_id'));

        // check if order is paid
        if ($order->isPaid()) {
            return response()->json([
                'message' => 'Order is already paid',
            ], 422);
        }

        // check if order is not expired
        if (!$order->renterCanPay()) {
            return response()->json([
                'message' => 'Order has expired',
            ], 422);
        }

        // get app currency
        $country = request()->header('Country');
        $currency = Country::findOrFail($country)->currency_code;

        // charge card
        $charge = $this->chargeCardToken(request('cardToken'), $order, $currency, request('cvv'));


        return $charge;
        // check if charge is successful
        if ($charge->statusCode != 200) {
            return response()->json([
                'message' => 'Charge failed',
            ], 422);
        }

        return $charge;
    }


    private function chargeCardToken($cardToken, $order, $currency, $cvv)
    {
        $fawry = new Fawry();
        $user = auth()->user();
        $merchantRefNum = $order->id;
        $total = number_format($order->total, 2, '.', '');
        $charge = $fawry->chargeViaCardToken($cardToken, $merchantRefNum, $user, $currency, $total, $cvv, $order->vehicle_id);

        return $charge;
    }
}
