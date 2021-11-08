<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use Uuid;

    public $timestamps = false;

    protected $fillable = [
        'name_en',
        'name_ar',
        'key'
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
}
