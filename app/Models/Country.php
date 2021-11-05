<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use Uuid;

    protected $fillable = [
        'name_en',
        'name_ar',
        'image',
        'country_code',
        'phone_prefix',
        'currency_code',
    ];

    protected $hidden = [
        'name_en',
        'name_ar',
    ];

    protected $appends = [
        'name',
    ];

    public function getNameAttribute()
    {
        return $this->{'name_' . app()->getLocale()};
    }

    public function states()
    {
        return $this->hasMany(State::class);
    }
}
