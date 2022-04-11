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
        'processing_percentage',
        'processing_fixed',
        'early_return_percentage',
        'owner_cancel_penality',
        'min_redemption_amount',
        'car_legal_download_1',
        'car_legal_download_2',
    ];

    protected $casts = [
        'version' => 'integer',
        'vat_percentage' => 'float',
        'point_to_money' => 'boolean',
        'point_to_money_rate' => 'float',
        'money_to_point' => 'boolean',
        'money_to_point_rate' => 'float',
        'processing_percentage' => 'float',
        'processing_fixed' => 'float',
        'early_return_percentage' => 'float',
        'owner_cancel_penality' => 'float',
        'min_redemption_amount' => 'integer',
    ];

    protected $hidden = [
        'id',
        'version',
        'updated_at',
        'created_at',
    ];

    public function getVatPercentageAttribute($value)
    {
        return $value / 100;
    }

    public function setVatPercentageAttribute($value)
    {
        return $value * 100;
    }

    public function getProcessingPercentageAttribute($value)
    {
        return $value / 100;
    }

    public function setProcessingPercentageAttribute($value)
    {
        return $value * 100;
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

    public function getCarLegalDownload1Attribute($value)
    {
        return url($this->attributes['car_legal_download_1']);
    }

    public function getCarLegalDownload_1Attribute()
    {
        return url($this->attributes['car_legal_download_2']);
    }

    public function getCarLegalDownload2Attribute($value)
    {
        return url($this->attributes['car_legal_download_2']);
    }

    public function getCarLegalDownload_2Attribute()
    {
        return url($this->attributes['car_legal_download_2']);
    }

}
