<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\VehicleInsurance
 */
class VehicleInsurance extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'vehicle_id',
        'expire_at',
        'verified_at',
        'image',
    ];

    protected $hidden = [
        'id',
        'verified_at',
        'vehicle_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];
}
