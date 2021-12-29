<?php

namespace App\Models;

use App\Traits\OrderedUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\Order
 *
 * @property string $id
 * @property string $renter_id
 * @property string $vehicle_id
 * @property string $owner_id
 * @property string $order_status_id
 * @property string $type
 * @property string|null $extended_from_id
 * @property string $start_date
 * @property string $end_date
 * @property int $day_count
 * @property string $day_cost
 * @property string $total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDayCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDayCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereExtendedFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOrderStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereVehicleId($value)
 * @mixin \Eloquent
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
        'user',
        'owner',
        'vehicle_total',
        'driver_total',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
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
    ];

    public function getVehicleAttribute()
    {
        $data = $this->vehicle()->first()->makeVisible('thumbnail');
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

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function owner()
    {
        return $this->hasOne(User::class, 'id', 'owner_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'order_id', 'id');
    }

    public function refund()
    {
        return $this->hasOne(OrderRefund::class, 'order_id', 'id');
    }

    public function isPaid()
    {
        return $this->invoice()->exists();
    }

    // scope overlaps dates
    public function scopeOverlaps($query, $start_date, $end_date)
    {
        return $query->where(function ($query) use ($start_date, $end_date) {
            $query->where(function ($query) use ($start_date,) {
                $query->where('start_date', '<=', $start_date)
                    ->where('end_date', '>=', $start_date);
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
        return $this->order_status_id == 1 /*&& $this->orderExpireAt() > Carbon::now()*/;
    }

    public function renterCanPay()
    {
        return $this->order_status_id == 3 && $this->paymentExpireAt() > Carbon::now();
    }

    public function renterCanCancel()
    {
        return $this->order_status_id <= 3;
    }

    public function ownerCanCancel()
    {
        return $this->order_status_id < 7 && $this->order_status_id > 3;
    }

    public function ownerCanReject()
    {
        return $this->order_status_id <= 3;
    }

    public function renterCanRequestRefund()
    {
        return $this->order_status_id == 11 && $this->payment()->paid && $this->refund()->isEmpty();
    }

    public function renterCanExtend()
    {
        return $this->order_status_id == 7 && $this->payment()->paid;
    }
}
