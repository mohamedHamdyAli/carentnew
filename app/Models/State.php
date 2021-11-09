<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\State
 *
 * @property string $id
 * @property string $country_id
 * @property string $name_en
 * @property string $name_ar
 * @property-read \App\Models\Country $country
 * @property-read mixed $name
 * @method static \Illuminate\Database\Eloquent\Builder|State newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|State newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|State query()
 * @method static \Illuminate\Database\Eloquent\Builder|State whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|State whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|State whereNameAr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|State whereNameEn($value)
 * @mixin \Eloquent
 */
class State extends Model
{
    use Uuid;

    public $timestamps = false;

    protected $fillable = [
        'name_en',
        'name_ar',
        'country_id',
    ];

    protected $hidden = [
        'country_id',
        'name_en',
        'name_ar'
    ];

    protected $appends = [
        'name',
    ];

    public function getNameAttribute()
    {
        return $this->{'name_' . app()->getLocale()};
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
