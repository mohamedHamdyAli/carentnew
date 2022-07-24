<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DriverLicense
 *
 * @property string $id
 * @property string $user_id
 * @property string $front_image
 * @property string $back_image
 * @property string $expire_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|DriverLicense newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DriverLicense newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DriverLicense query()
 * @method static \Illuminate\Database\Eloquent\Builder|DriverLicense whereBackImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DriverLicense whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DriverLicense whereExpireAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DriverLicense whereFrontImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DriverLicense whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DriverLicense whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DriverLicense whereUserId($value)
 * @mixin \Eloquent
 */
class DriverLicense extends Model
{
    use Uuid;

    protected $fillable = [
        'user_id',
        'front_image',
        'back_image',
        'expire_at',
    ];

    protected $dates = [
        'expire_at',
    ];

    protected $hidden = [
        'id',
        'user_id',
        'created_at',
        'updated_at',
    ];
}
