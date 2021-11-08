<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use Uuid;

    public $timestamps = false;

    protected $fillable = [
        'display_order',
        'name_en',
        'name_ar',
    ];

    protected $hidden = [
        'display_order',
        'name_en',
        'name_ar',
    ];

    protected $appends = [
        'name'
    ];

    public function getNameAttribute()
    {
        return $this->{'name_' . app()->getLocale()};
    }
}
