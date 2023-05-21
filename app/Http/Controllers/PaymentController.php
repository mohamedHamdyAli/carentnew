<?php

namespace App\Http\Controllers;

use App\Consts\Status;
use App\Models\Country;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderExtend;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
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

        if (request('extend') && request('extend') == true) {
            $orderExtend = OrderExtend::where('order_id', $order->id)
                ->where('approved', true)
                ->where('paid', false)
                ->orderBy('created_at', 'desc')
                ->first();
            if (!($orderExtend && $orderExtend->renterCanPay())) {
                return response()->json([
                    'message' => __('messages.r_failed'),
                    'data' => null,
                    'error' => 'Order can not be paid',
                ]);
            }
        }

        // check if order is paid
        if ($order->isPaid()) {
            return response()->json([
                'message' => __('messages.r_failed'),
                'data' => null,
                'error' => 'Order is already paid',
            ], 400);
        }

        // check if order is not expired
        if (!$order->renterCanPay()) {
            return response()->json([
                'message' => __('messages.r_failed'),
                'data' => null,
                'error' => 'Order can not be paid',
            ], 400);
        }

        // get app currency
        $country = request()->header('Country');
        $currency = Country::findOrFail($country)->currency_code;
        $total = number_format($order->total, 2, '.', '');

        // charge card
        $charge = $this->chargeCardToken(request('cardToken'), $order, $currency, request('cvv'), $total, $order->number);

        // check if charge is successful
        if ($charge->statusCode != 200) {
            return response()->json([
                'message' => __('messages.error.payment_failed'),
                'data' => app()->environment('production') ? null : $charge,
                'error' => 'Failed to charge card',
            ], 400);
        }

        // save payment
        Log::info('charge', [$charge]);
        $payment = Payment::create([
            'type' => $charge->type,
            'referenceNumber' => $charge->referenceNumber,
            'merchantRefNumber' => $charge->merchantRefNumber,
            'orderAmount' => $charge->orderAmount,
            'paymentAmount' => $charge->paymentAmount,
            'fawryFees' => $charge->fawryFees,
            'paymentMethod' => $charge->paymentMethod,
            'orderStatus' => $charge->orderStatus,
            'paymentTime' => Carbon::parse($charge->paymentTime),
            'customerMobile' => @$charge->customerMobile,
            'customerMail' => @$charge->customerMail,
            'customerProfileId' => $charge->customerProfileId,
            'signature' => $charge->signature,
            'statusCode' => $charge->statusCode,
            'statusDescription' => $charge->statusDescription,
        ]);

        // save invoice
        Invoice::create(
            [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'sub_total' => $order->sub_total,
                'vat' => $order->vat,
                'discount' => $order->discount,
                'total' => $order->total,
                'currency' => $currency,
            ]
        );

        // update order status
        $order->order_status_id = Status::PAID;
        $order->save();
        $order->order_status_id = Status::CONFIRMED;
        $order->save();

        return response()->json([
            'message' => __('messages.success.payment_success'),
            'data' => null,
            'error' => null,
        ], 200);
    }

    public function payExtend()
    {
        $this->validate(request(), [
            'cardToken' => 'required',
            'order_id' => 'required|string|exists:orders,id',
            'cvv' => 'required|string|size:3',
        ]);

        // get order
        $order = Order::findOrFail(request('order_id'));

        $orderExtend = OrderExtend::where('order_id', $order->id)
            ->where('approved', true)
            ->where('paid', false)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!($orderExtend && $orderExtend->renterCanPay())) {
            return response()->json([
                'message' => __('messages.r_failed'),
                'data' => null,
                'error' => 'Order can not be paid',
            ]);
        }

        // get app currency
        $country = request()->header('Country');
        $currency = Country::findOrFail($country)->currency_code;
        $total = number_format($orderExtend->total, 2, '.', '');

        // charge card
        $charge = $this->chargeCardToken(request('cardToken'), $order, $currency, request('cvv'), $total, $order->number);
        // check if charge is successful
        if ($charge->statusCode != 200) {
            return response()->json([
                'message' => __('messages.error.payment_failed'),
                'data' => app()->environment('production') ? null : $charge,
                'error' => 'Failed to charge card',
            ], 400);
        }

        // save payment
        $payment = Payment::create([
            'type' => $charge->type,
            'referenceNumber' => $charge->referenceNumber,
            'merchantRefNumber' => $charge->merchantRefNumber,
            'orderAmount' => $charge->orderAmount,
            'paymentAmount' => $charge->paymentAmount,
            'fawryFees' => $charge->fawryFees,
            'paymentMethod' => $charge->paymentMethod,
            'orderStatus' => $charge->orderStatus,
            'paymentTime' => Carbon::parse($charge->paymentTime),
            'customerMobile' => @$charge->customerMobile,
            'customerMail' => @$charge->customerMail,
            'customerProfileId' => $charge->customerProfileId,
            'signature' => $charge->signature,
            'statusCode' => $charge->statusCode,
            'statusDescription' => $charge->statusDescription,
        ]);

        // save invoice
        Invoice::create(
            [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'sub_total' => $order->sub_total,
                'vat' => $order->vat,
                'discount' => $order->discount,
                'total' => $order->total,
                'currency' => $currency,
            ]
        );

        // update extend is paid
        $orderExtend->paid = true;
        $orderExtend->save();

        $order->vehicle_total += $orderExtend->vehicle_total;
        $order->driver_total += $orderExtend->driver_total;
        $order->sub_total += $orderExtend->sub_total;
        $order->vat += $orderExtend->vat;
        $order->discount += $orderExtend->discount;
        $order->total += $orderExtend->total;
        $order->end_date = $orderExtend->request_end_date;

        // update order status
        $order->order_status_id = Status::PAID;
        $order->save();
        $order->order_status_id = Status::CAR_DELIVERED;
        $order->save();

        return response()->json([
            'message' => __('messages.success.payment_success'),
            'data' => null,
            'error' => null,
        ], 200);
    }


    private function chargeCardToken($cardToken, $order, $currency, $cvv, $total, $ref)
    {
        $fawry = new Fawry();
        $user = auth()->user();
        $merchantRefNum = $ref;
        $charge = $fawry->chargeViaCardToken($cardToken, $merchantRefNum . '-' . Carbon::now()->format('YmdHis'), $user, $currency, $total, $cvv, $order->vehicle_id);

        return $charge;
    }
}
