<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\VehicleInsurance
 *
 * @property string $id
 * @property string $vehicle_id
 * @property string $front_image
 * @property string $back_image
 * @property string|null $approved_at
 * @property string|null $expire_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleInsurance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleInsurance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleInsurance query()
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleInsurance whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleInsurance whereBackImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleInsurance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleInsurance whereExpireAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleInsurance whereFrontImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleInsurance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleInsurance whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleInsurance whereVehicleId($value)
 * @mixin \Eloquent
 */
class VehicleInsurance extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'vehicle_id',
        'expire_at',
        'image',
    ];

    protected $hidden = [
        'id',
        'vehicle_id',
        'created_at',
        'updated_at',
    ];
}
