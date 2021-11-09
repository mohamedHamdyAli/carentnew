<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Privilege
 *
 * @property string $id
 * @property string $name_en
 * @property string $name_ar
 * @property string $key
 * @property-read mixed $name
 * @method static \Illuminate\Database\Eloquent\Builder|Privilege newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Privilege newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Privilege query()
 * @method static \Illuminate\Database\Eloquent\Builder|Privilege whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Privilege whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Privilege whereNameAr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Privilege whereNameEn($value)
 * @mixin \Eloquent
 */
class Privilege extends Model
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
