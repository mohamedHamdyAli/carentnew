<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\VehiclePricing
 *
 * @property string $id
 * @property string $vehicle_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|VehiclePricing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VehiclePricing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VehiclePricing query()
 * @method static \Illuminate\Database\Eloquent\Builder|VehiclePricing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehiclePricing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehiclePricing whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehiclePricing whereVehicleId($value)
 * @mixin \Eloquent
 */
class VehiclePricing extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'vehicle_id',
        'daily_price',
        'week_to_month',
        'month_or_more',
        'has_driver',
        'driver_daily_price',
        'is_driver_required',
    ];

    protected $casts = [
        'daily_price' => 'float',
        'week_to_month' => 'float',
        'month_or_more' => 'float',
        'has_driver' => 'boolean',
        'driver_daily_price' => 'float',
        'is_driver_required' => 'boolean',
    ];

    protected $hidden = [
        'id',
        'vehicle_id',
        'created_at',
        'updated_at',
    ];
}
