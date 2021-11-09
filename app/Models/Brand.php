<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Brand
 *
 * @property string $id
 * @property int|null $display_order
 * @property string $name_en
 * @property string $name_ar
 * @property string|null $logo
 * @property-read mixed $name
 * @method static \Illuminate\Database\Eloquent\Builder|Brand newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Brand newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Brand query()
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereDisplayOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereNameAr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereNameEn($value)
 * @mixin \Eloquent
 */
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
