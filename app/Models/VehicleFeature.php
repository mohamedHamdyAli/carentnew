<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\VehicleFeature
 *
 * @property int $id
 * @property string $name_en
 * @property string $name_ar
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleFeature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleFeature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleFeature query()
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleFeature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleFeature whereNameAr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleFeature whereNameEn($value)
 * @mixin \Eloquent
 */
class VehicleFeature extends Model
{
    use HasFactory;
}
