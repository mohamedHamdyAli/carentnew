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
        'notify',
        'message_en',
        'message_ar',
    ];
}
