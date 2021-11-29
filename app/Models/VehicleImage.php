<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\VehicleImage
 *
 * @property string $id
 * @property string $vehicle_id
 * @property int $displayOrder
 * @property string $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleImage query()
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleImage whereDisplayOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleImage whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleImage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleImage whereVehicleId($value)
 * @mixin \Eloquent
 */
class VehicleImage extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'vehicle_id',
        'display_order',
        'image',
    ];

    protected $hidden = [
        'vehicle_id',
        'created_at',
        'updated_at',
    ];
}
