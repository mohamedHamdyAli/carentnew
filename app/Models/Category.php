<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use Uuid;

    protected $fillable = [
        'name_en',
        'name_ar',
    ];

    protected $hidden = [
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
