<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Uuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'default_address_id',
        'balance',
        'reward_points',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'default_address_id',
        'balance',
        'reward_points',
    ];

    protected $appends = [
        'default_address',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'balance'       => 'float',
        'reward_points' => 'integer',
    ];

    /**
     * Returns user addresses.
     *
     * @var array
     */

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function getDefaultAddressAttribute()
    {
        return Address::where('id', $this->default_address_id)->first() ?? null;
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isEmailVerified()
    {
        return $this->email_verified_at !== null;
    }

    public function isPhoneVerified()
    {
        return $this->phone_verified_at !== null;
    }
}
