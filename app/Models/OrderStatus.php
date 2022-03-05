<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderStatus
 */
class OrderStatus extends Model
{

    public $timestamps = false;

    protected $fillable = [
        'name_en',
        'name_ar',
        'terminate',
        'notify_client',
        'notify_owner',
        'client_title_en',
        'client_title_ar',
        'client_body_en',
        'client_body_ar',
        'owner_title_en',
        'owner_title_ar',
        'owner_body_en',
        'owner_body_ar',
        'alert_type',
        'filterable',
    ];

    protected $hidden = [
        'name_en',
        'name_ar',
        'notify_client',
        'notify_owner',
        'client_title_en',
        'client_title_ar',
        'client_body_en',
        'client_body_ar',
        'owner_title_en',
        'owner_title_ar',
        'owner_body_en',
        'owner_body_ar',
        'filterable',
    ];

    protected $appends = [
        'name',
    ];

    public function getNameAttribute()
    {
        return $this->{'name_' . app()->getLocale()};
    }
}
