<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Address
 *
 * @property string $id
 * @property string|null $user_id
 * @property string $name
 * @property string|null $company
 * @property string $address_0
 * @property string|null $address_1
 * @property string $country_id
 * @property string $state_id
 * @property string $city
 * @property string|null $post_code
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Address newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Address newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Address query()
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereAddress0($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address wherePostCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereUserId($value)
 * @mixin \Eloquent
 */
class Address extends Model
{
    use Uuid;

    protected $fillable = [
        'user_id',
        'name',
        'company',
        'address_0',
        'address_1',
        'country_id',
        'state_id',
        'city',
        'post_code',
        'hint',
    ];

    protected $hidden = [
        'user_id',
        'country_id',
        'state_id',
        'user',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'is_default',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->hasOne(Country::class);
    }

    public function state()
    {
        return $this->hasOne(State::class);
    }

    public function getIsDefaultAttribute()
    {
        return $this->user->default_address_id === $this->id;
    }
    
}
