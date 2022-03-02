<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CacheHelper;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Cache;
use Illuminate\Http\Request;

class AdminPaymentController extends Controller
{
    public function index()
    {
        // validate request
        $this->validate(request(), [
            'page'          => 'sometimes|integer|min:1',
            'per_page'      => 'sometimes|integer|min:1',
            'search'        => 'sometimes|string',
            'statuses'      => 'sometimes|array|in:PAID,FAILED',
        ]);

        $data = Cache::tags(['payments'])->remember(CacheHelper::makeKey('payments'), 600, function () {
            $payments = Payment::query();

            // filter by status
            if (request()->has('statuses')) {
                $payments = $payments->whereIn('orderStatus', request('statuses'));
            }

            // search
            if (request()->has('search')) {
                $payments = $payments->whereHas('customer', function ($query) {
                    return $query->where('name', 'like', '%' . request('search') . '%')
                        ->orWhere('email', 'like', '%' . request('search') . '%')
                        ->orWhere('phone', 'like', '%' . request('search') . '%');
                })
                    ->orWhere('referenceNumber', 'like', '%' . request('search') . '%')
                    ->orWhere('merchantRefNumber', 'like', '%' . request('search') . '%')
                    ->orWhere('orderAmount', 'like', '%' . request('search') . '%');
            }

            $payments = $payments
                ->orderBy('paymentTime', 'desc')
                ->paginate(request('per_page', 20));

            // add created_at to users
            $payments = $payments->setCollection($payments->getCollection()->map(function ($payment) {
                return $payment->makeHidden([
                    'type',
                    'customerMobile',
                    'customerMail',
                    'customerProfileId',
                    'signature',
                    'statusCode',
                    'statusDescription',
                ]);
            }));

            return $payments;
        });

        return response()->json($data);
    }

    public function show($id)
    {
        $payment = Payment::with([
            'customer',
            'invoice',
            'invoice.order',
        ])->findOrFail($id);

        return response()->json($payment);
    }
}
