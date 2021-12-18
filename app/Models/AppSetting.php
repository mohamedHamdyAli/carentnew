<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'vat_percentage',
        'point_to_money',
        'point_to_money_rate',
        'money_to_point',
        'money_to_point_rate',
    ];

    protected $casts = [
        'vat_percentage' => 'float',
        'point_to_money' => 'boolean',
        'point_to_money_rate' => 'float',
        'money_to_point' => 'boolean',
        'money_to_point_rate' => 'float',
    ];

    public function getVatPercentageAttribute($value)
    {
        return $value / 100;
    }

    public function setVatPercentageAttribute($value)
    {
        $this->attributes['vat_percentage'] = $value * 100;
    }
}
