<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Setting
 *
 * @property int $id
 * @property string $key
 * @property string $value
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereValue($value)
 * @mixin \Eloquent
 */
class Setting extends Model
{
    use Uuid;

    public $timestamps = false;

    protected $fillable = [
        'key',
        'name_en',
        'name_ar',
        'content_en',
        'content_ar',
    ];

    protected $hidden = [
        'id',
        'key',
        'name_en',
        'name_ar',
        'content_en',
        'content_ar',
    ];

    protected $casts = [
        'content_en' => 'json',
        'content_ar' => 'json',
    ];

    protected $appends = [
        'name',
        'content',
    ];

    public function getNameAttribute()
    {
        return $this->{'name_' . app()->getLocale()};
    }

    public function getContentAttribute()
    {
        return $this->{'content_' . app()->getLocale()};
    }
}
