<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserOtp
 *
 * @property string $id
 * @property string $user_id
 * @property int $opt
 * @property string $for
 * @property string|null $expire_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserOtp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserOtp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserOtp query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserOtp whereExpireAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOtp whereFor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOtp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOtp whereOpt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOtp whereUserId($value)
 * @mixin \Eloquent
 */
class UserOtp extends Model
{
    use Uuid;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'otp',
        'for',
        'expire_at',
    ];

}
