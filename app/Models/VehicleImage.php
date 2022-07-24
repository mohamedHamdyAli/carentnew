<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\VehicleImage
 */
class VehicleImage extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'vehicle_id',
        'display_order',
        'image',
    ];

    protected $hidden = [
        'vehicle_id',
        'created_at',
        'updated_at',
    ];
}
