<?php

namespace App\Models;

use App\Traits\Version;
use App\Traits\OrderedUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    use HasFactory, OrderedUuid, Version;

    protected $fillable = [
        'version',
        'vat_percentage',
        'point_to_money',
        'point_to_money_rate',
        'money_to_point',
        'money_to_point_rate',
    ];

    protected $casts = [
        'version' => 'integer',
        'vat_percentage' => 'float',
        'point_to_money' => 'boolean',
        'point_to_money_rate' => 'float',
        'money_to_point' => 'boolean',
        'money_to_point_rate' => 'float',
    ];

    protected $hidden = [
        'id',
        'version',
        'updated_at',
    ];

    public function getVatPercentageAttribute($value)
    {
        return $value / 100;
    }

    public function setVatPercentageAttribute($value)
    {
        $this->attributes['vat_percentage'] = $value * 100;
    }

    static function getLastVersion()
    {
        return AppSetting::orderBy('version', 'desc')->first();
    }

    public function getNextVersion(): int
    {
        $last = $this->orderBy('version', 'desc')->first();
        return  $last ? $last->version + 1 : 1;
    }
}
