<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

class BrandModel extends Model
{
    use Uuid;

    public $timestamps = false;

    protected $fillable = [
        'name_en',
        'name_ar',
        'brand_id',
        'display_order'
    ];

    protected $hidden = [
        'name_en',
        'name_ar',
        'brand_id',
        'display_order'
    ];

    protected $appends = [
        'name'
    ];

    public function getNameAttribute()
    {
        return $this->{'name_' . app()->getLocale()};
    }
}
