<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FuelType extends Model
{
    use Uuid,SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'display_order',
        'name_en',
        'name_ar',
        'active'
    ];

    protected $hidden = [
        'display_order',
        'name_en',
        'name_ar',
        'deleted_at',
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    protected $appends = [
        'name'
    ];

    public function getNameAttribute()
    {
        return $this->{'name_' . app()->getLocale()};
    }
}
