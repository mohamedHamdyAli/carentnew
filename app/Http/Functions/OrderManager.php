<?php

namespace App\Http\Functions;

use App\Models\AppSetting;
use App\Models\Order;
use App\Models\VehiclePricing;
use Carbon\Carbon;
use Route;

class OrderManager
{
    public static function getTotals($vehicle_id, $start_date, $end_date, $hasDriver, $suggestedPrice = 0)
    {
        $appSettings = AppSetting::getLastVersion();
        $pricing = VehiclePricing::where('vehicle_id', $vehicle_id)->first();
        $daysCount = Carbon::parse($start_date)->diffInDays(Carbon::parse($end_date));
        $suggestedPrice = intval($suggestedPrice);

        if ($daysCount < 7) $dayPrice = $pricing->daily_price;
        else if ($daysCount < 30) $dayPrice = $pricing->week_to_month ?? $pricing->daily_price;
        else $dayPrice = $pricing->month_or_more ?? $pricing->daily_price;

        $driverDayPrice = $pricing->driver_daily_price;

        $vehicleTotal = $dayPrice * $daysCount;
        $driverTotal = $hasDriver ? $driverDayPrice * $daysCount : 0;
        $subTotal = $vehicleTotal + $driverTotal;
        $vat = $subTotal * $appSettings->vat_percentage;
        $originalTotal = $subTotal + $vat;
        $discount = $suggestedPrice > 0 ? ($originalTotal - $suggestedPrice) : 0;

        $totals = [
            'vehicle_total' => $vehicleTotal,
            'driver_total' => $driverTotal,
            'sub_total' => $subTotal,
            'vat' => Round($vat),
            'days_count' => $daysCount,
            'original_total' => $originalTotal,
            'discount' => $discount,
            'total' => intval($suggestedPrice > 0 ? $suggestedPrice : ($originalTotal - $discount)),
        ];

        return $totals;
    }


    public static function generateNumber()
    {
        $unique_number = 0;
        $number = 88 - (int) Carbon::now()->format('y');
        $number = 500 . $number;
        $day = 777 - (int) Carbon::now()->format('z');
        $today_orders_count = Order::whereDate('created_at', Carbon::today())->count();
        $unique_number = (int) ($number . str_pad($day, 3, '0', STR_PAD_LEFT) . str_pad((int) ($today_orders_count + 1), 3, '0', STR_PAD_LEFT));
        $isExist = Order::where('number', $unique_number)->first();
        if ($isExist) {
            return OrderManager::generateNumber();
        }
        return $unique_number;
    }
}
