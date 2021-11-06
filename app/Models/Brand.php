<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use Uuid;

    public $timestamps = false;

    protected $fillable = [
        'display_order',
        'name_en',
        'name_ar',
        'logo',
    ];

    protected $hidden = [
        'name_en',
        'name_ar',
        'display_order'
    ];

    protected $appends = [
        'name',
    ];

    public function getNameAttribute()
    {
        return $this->{'name_' . app()->getLocale()};
    }
}
