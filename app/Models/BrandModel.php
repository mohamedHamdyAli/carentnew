<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BrandModel
 *
 * @property string $id
 * @property string $brand_id
 * @property int|null $display_order
 * @property string $name_en
 * @property string $name_ar
 * @property-read mixed $name
 * @method static \Illuminate\Database\Eloquent\Builder|BrandModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BrandModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BrandModel query()
 * @method static \Illuminate\Database\Eloquent\Builder|BrandModel whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BrandModel whereDisplayOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BrandModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BrandModel whereNameAr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BrandModel whereNameEn($value)
 * @mixin \Eloquent
 */
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
