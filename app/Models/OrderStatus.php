<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderStatus
 *
 * @property int $id
 * @property string $name_en
 * @property string $name_ar
 * @property string $description_en
 * @property string $description_ar
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatus whereDescriptionAr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatus whereDescriptionEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatus whereNameAr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatus whereNameEn($value)
 * @mixin \Eloquent
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
    ];

    protected $appends = [
        'name',
    ];

    public function getNameAttribute()
    {
        return $this->{'name_' . app()->getLocale()};
    }
}
