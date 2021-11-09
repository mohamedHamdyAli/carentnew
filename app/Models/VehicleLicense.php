<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\VehicleLicense
 *
 * @property string $id
 * @property string $vehicle_id
 * @property string $state_id
 * @property string $front_image
 * @property string $back_image
 * @property string|null $expire_at
 * @property string|null $approved_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleLicense newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleLicense newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleLicense query()
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleLicense whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleLicense whereBackImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleLicense whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleLicense whereExpireAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleLicense whereFrontImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleLicense whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleLicense whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleLicense whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleLicense whereVehicleId($value)
 * @mixin \Eloquent
 */
class VehicleLicense extends Model
{
    use HasFactory, Uuid;
}
