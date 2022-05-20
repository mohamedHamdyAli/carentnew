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
        'vat',
        'profit_margin',
        'renter_cancellation_fees',
        'owner_cancellation_fees',
        'late_retern_fees',
        'early_retern_fees',
        'accident_fees',
        'accident_max_fees',
        'money_to_point_rate',
        'point_to_money_rate',
        'min_redemption_amount',
        'rental_contract_file',
        'vehicle_receive_file',
        'vehicle_return_file',
    ];

    protected $casts = [
        'version' => 'integer',
        'vat' => 'float',
        'profit_margin' => 'float',
        'renter_cancellation_fees' => 'float',
        'owner_cancellation_fees' => 'float',
        'late_retern_fees' => 'float',
        'early_retern_fees' => 'float',
        'accident_fees' => 'float',
        'accident_max_fees' => 'float',
        'money_to_point_rate' => 'float',
        'point_to_money_rate' => 'float',
        'min_redemption_amount' => 'integer',
    ];

    protected $hidden = [
        'id',
        'version',
        'updated_at',
        'created_at',
    ];

    static function getLastVersion()
    {
        return AppSetting::orderBy('version', 'desc')->first();
    }

    public function getNextVersion(): int
    {
        $last = $this->orderBy('version', 'desc')->first();
        return  $last ? $last->version + 1 : 1;
    }

    public function getVehicleReceiveFileAttribute($value)
    {
        return asset($this->attributes['vehicle_receive_file'], true);
    }

    public function getRentalContractFileAttribute()
    {
        return asset($this->attributes['rental_contract_file'], true);
    }

    public function getVehicleReturnFileAttribute($value)
    {
        return asset($this->attributes['vehicle_return_file'], true);
    }
}
