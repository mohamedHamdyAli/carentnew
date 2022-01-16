<?php

namespace App\Models;

use App\Traits\OrderedUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderExtend extends Model
{
    use HasFactory, OrderedUuid;

    protected $fillable = [
        'order_id',
        'original_end_date',
        'request_end_date',
        'approved',
        'with_driver',
        'vehicle_total',
        'driver_total',
        'sub_total',
        'vat',
        'discount',
        'total',
        'paid',
    ];

    protected $hidden = [
        'with_driver',
        'vehicle_total',
        'driver_total',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'approved' => 'boolean',
        'with_driver' => 'boolean',
        'vehicle_total' => 'float',
        'driver_total' => 'float',
        'sub_total' => 'float',
        'vat' => 'float',
        'discount' => 'float',
        'total' => 'float',
        'paid' => 'boolean',
    ];

    // scope for approved orders
    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }

    // scope for pending orders
    public function scopePending($query)
    {
        return $query->where('approved', true)
            ->orWhere('approved', null)
            ->where('paid', false);
    }

    public function orderExpireAt()
    {
        $updatedAt = $this->updated_at;
        $orderExpireAfterMinutes = config('app.order_expire_after');
        return $updatedAt->addMinutes($orderExpireAfterMinutes);
    }

    public function paymentExpireAt()
    {
        $updatedAt = $this->updated_at;
        $paymentExpireAfterMinutes = config('app.payment_expire_after');
        return $updatedAt->addMinutes($paymentExpireAfterMinutes);
    }

    // can be paid orders
    public function renterCanPay()
    {
        return Carbon::now()->diffInMinutes($this->paymentExpireAt(), false) > 0 && $this->paid == false && $this->approved == true;
    }

    // can be approved
    public function ownerCanAccept()
    {
        return Carbon::now()->diffInMinutes($this->orderExpireAt(), false) > 0 && $this->approved == null;
    }

    // active orders
    public function isActive()
    {
        return $this->renterCanPay() || $this->ownerCanAccept();
    }
}
