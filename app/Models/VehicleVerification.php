<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleVerification extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'vehicle_id',
        'vehicle_license_id',
        'vehicle_license_verified',
        'vehicle_insurance_id',
        'vehicle_insurance_verified',
        'status',
        'reason',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'terms_agreed' => 'boolean',
        'vehicle_insurance_verified' => 'boolean',
        'vehicle_license_verified' => 'boolean',
        'approved' => 'boolean',
    ];

    /**
     * The attributes that should be hidden.
     */
    protected $hidden = [
        'id',
        'vehicle_id',
        'vehicle_insurance_id',
        'vehicle_license_id',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'vehicle_insurance_uploaded',
        'vehicle_license_uploaded',
        'approved',
    ];

    /**
     * Get the user that owns the application.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getApprovedAttribute()
    {
        return $this->status === 'approved';
    }

    /**
     * Get the identity document that owns the application.
     */
    public function vehicleInsurance()
    {
        return $this->hasMany(VehicleInsurance::class, 'vehicle_id', 'vehicle_id');
    }

    public function getVehicleInsuranceUploadedAttribute()
    {
        return $this->vehicleInsurance()
            ->orderBy('created_at', 'desc')
            ->first([
                'id', 'vehicle_id', 'verified_at', 'created_at'
            ]) ? true : false;
    }

    /**
     * Get the driver license that owns the application.
     */
    public function vehicleLicense()
    {
        return $this->hasMany(VehicleLicense::class, 'vehicle_id', 'vehicle_id');
    }

    public function getVehicleLicenseUploadedAttribute()
    {
        return $this->vehicleLicense()
            ->orderBy('created_at', 'desc')
            ->first([
                'id', 'vehicle_id', 'verified_at', 'created_at'
            ]) ? true : false;
    }
}
