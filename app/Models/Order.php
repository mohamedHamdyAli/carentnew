<?php

namespace App\Models;

use App\Consts\Status;
use App\Traits\OrderedUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\Order
 */
class Order extends Model
{
    use OrderedUuid;

    protected $fillable = [
        'number',
        'user_id',
        'vehicle_id',
        'owner_id',
        'order_status_id',
        'start_date',
        'end_date',
        'with_driver',
        'vehicle_total',
        'driver_total',
        'sub_total',
        'vat',
        'discount',
        'total',
    ];

    protected $hidden = [
        'user_id',
        'vehicle_id',
        'owner_id',
        // 'user',
        // 'owner',
        'vehicle_total',
        'driver_total',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'with_driver' => 'boolean',
        'order_status_id' => 'integer',
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
        'with_driver' => 'boolean',
        'vehicle_total' => 'float',
        'driver_total' => 'float',
        'sub_total' => 'float',
        'vat' => 'float',
        'discount' => 'float',
        'total' => 'float',
    ];

    protected $appends = [
        'vehicle',
        'status',
        'invoices',
    ];

    public function getVehicleAttribute()
    {
        $data = $this->vehicle()->first();
        if ($data) {
            $data->makeVisible('thumbnail');
        } else {
            return null;
        }
        $data->thumbnail = url(Storage::url($data->thumbnail));
        return $data;
    }

    public function getStatusAttribute()
    {
        return $this->orderStatus()->first();
    }

    public function vehicle()
    {
        return $this->hasOne(Vehicle::class, 'id', 'vehicle_id');
    }

    public function orderStatus()
    {
        return $this->hasOne(OrderStatus::class, 'id', 'order_status_id');
    }

    public function orderStatusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class, 'order_id', 'id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function renter()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function owner()
    {
        return $this->hasOne(User::class, 'id', 'owner_id');
    }

    public function OrderInvocies()
    {
        return $this->hasMany(Invoice::class, 'order_id', 'id');
    }

    public function getInvoicesAttribute()
    {
        return $this->OrderInvocies()->get();
    }

    public function refunds()
    {
        return $this->hasMany(OrderRefund::class, 'order_id', 'id');
    }

    public function isPaid()
    {
        return $this->OrderInvocies()->get()->count() > 0;
    }

    // scope overlaps dates
    public function scopeOverlaps($query, $start_date, $end_date)
    {
        return $query->whereHas('OrderStatus', function ($query) {
            $query->where('terminate', false);
        })
            ->where(function ($query) use ($start_date, $end_date) {
                $query->where(function ($query) use ($start_date,) {
                    $query->where('start_date', '<=', $start_date)
                        ->where('end_date', '>', $start_date);
                })->orWhere(function ($query) use ($end_date) {
                    $query->where('start_date', '<=', $end_date)
                        ->where('end_date', '>=', $end_date);
                })->orWhere(function ($query) use ($start_date, $end_date) {
                    $query->where('start_date', '>=', $start_date)
                        ->where('end_date', '<=', $end_date);
                });
            });
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

    public function ownerCanAccept()
    {
        return $this->order_status_id == Status::CREATED
            && Carbon::now()->diffInMinutes($this->paymentExpireAt(), false) > 0;
    }

    public function renterCanPay()
    {
        // return true;
        return $this->order_status_id == Status::PENDING_PAYMENT
            && Carbon::now()->diffInMinutes($this->orderExpireAt(), false) > 0;
    }

    public function renterCanCancel()
    {
        return $this->order_status_id <= Status::PENDING_PAYMENT;
    }

    public function ownerCanCancel()
    {
        return $this->order_status_id < Status::CAR_DELIVERED
            && $this->order_status_id > 3;
    }

    public function ownerCanReject()
    {
        return $this->order_status_id <= Status::PENDING_PAYMENT;
    }

    public function renterCanRequestRefund()
    {
        return $this->order_status_id == Status::CANCELED
            && $this->payment()->paid
            && $this->refund()->isEmpty();
    }

    public function OrderExtends()
    {
        return $this->hasMany(OrderExtend::class, 'order_id', 'id');
    }

    public function activeExtendRequests()
    {
        return $this->OrderExtends()->pending()->get();
    }

    public function renterCanExtend()
    {
        $lastExtendRequest = $this->OrderExtends()->orderBy('created_at', 'desc')->first();
        return $this->order_status_id == Status::CAR_DELIVERED
            && !($lastExtendRequest && $lastExtendRequest->isActive());
    }

    public function onwerCanHandleExtendRequest()
    {
        $lastExtendRequest = $this->OrderExtends()->orderBy('created_at', 'desc')->first();
        return $this->order_status_id == Status::CAR_DELIVERED
            && $lastExtendRequest && $lastExtendRequest->isActive();
    }

    public function OrderEarlyReturn()
    {
        return $this->hasOne(OrderEarlyReturn::class);
    }

    public function renterCanReturnEarly()
    {
        return $this->order_status_id == Status::CAR_DELIVERED
            && $this->OrderEarlyReturn()->count() == 0
            && Carbon::tomorrow()->toDateString() < $this->end_date
            && Carbon::now()->toDateString() >= $this->start_date;
    }

    public function ownerCanCompleteOrder()
    {
        return ($this->order_status_id == Status::CAR_DELIVERED && $this->OrderEarlyReturn()->count() > 0)
            || ($this->order_status_id == Status::CAR_DELIVERED && $this->end_date <= Carbon::now()->toDateString());
    }

    public function ownerCanDeliver()
    {
        return $this->order_status_id == Status::CONFIRMED
            && Carbon::now()->toDateString() <= $this->end_date
            && Carbon::now()->toDateString() >= $this->start_date;
        // TODO: remove after testing
    }

    public function renterCanReceive()
    {
        return $this->order_status_id == Status::CAR_ARRIVED;
    }
}
