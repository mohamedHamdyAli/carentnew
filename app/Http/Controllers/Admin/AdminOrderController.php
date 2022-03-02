<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CacheHelper;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Cache;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index()
    {
        $results = Cache::tags(['orders'])->remember(CacheHelper::makeKey('orders-admin'), 3600, function () {
            $orders = Order::where('id', '!=', null);

            if (request()->has('search')) {
                $orders = $orders
                    ->where('number', 'like', '%' . request('search') . '%')
                    ->orWhereHas('vehicle', function ($query) {
                        return $query->where('plate_number', 'like', '%' . request('search') . '%');
                    })
                    ->orWhereHas('user', function ($query) {
                        return $query->where('name', 'like', '%' . request('search') . '%')
                            ->orWhere('email', 'like', '%' . request('search') . '%')
                            ->orWhere('phone', 'like', '%' . request('search') . '%');
                    })
                    ->orWhereHas('owner', function ($query) {
                        return $query->where('name', 'like', '%' . request('search') . '%')
                            ->orWhere('email', 'like', '%' . request('search') . '%')
                            ->orWhere('phone', 'like', '%' . request('search') . '%');
                    });
            }

            if (request()->has('from_date')) {
                $orders = $orders->where('created_at', '>=', request('from_date'));
            }

            if (request()->has('to_date')) {
                $orders = $orders->where('created_at', '<=', request('to_date'));
            }

            if (request()->has('start_date')) {
                $orders = $orders->where('start_date', '>=', request('start_date'));
            }

            if (request()->has('end_date')) {
                $orders = $orders->where('end_date', '<=', request('end_date'));
            }

            if (request()->has('statuses')) {
                $orders = $orders->whereIn('order_status_id', request('statuses'));
            }

            $orders = $orders->orderBy('created_at', 'desc')->paginate();
            $orders->setCollection(
                $orders->getCollection()
                    ->makeVisible(['created_at'])
                    ->makeHidden(['vehicle', 'invoices'])
            );

            return $orders;
        });

        return response()->json($results);
    }

    public function show($id)
    {
        $payment = Order::with([
            'user',
            'owner',
            'vehicle',
            'vehicle.brand',
            'orderStatusHistory',
            'orderStatusHistory.orderStatus',
        ])->findOrFail($id);

        $payment->makeVisible([
            'owner',
            'user',
            'vehicle.BrandModel.logo',
            'created_at',
        ]);

        return response()->json($payment);
    }
}
