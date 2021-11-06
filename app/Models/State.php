<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

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
