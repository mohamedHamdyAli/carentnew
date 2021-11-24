<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RenterApplication extends Model
{
    use HasFactory, Uuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'terms_agreed',
        'identity_document_id',
        'identity_document_verified',
        'driver_license_id',
        'driver_license_verified',
        'approved',
        'reason',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'terms_agreed' => 'boolean',
        'identity_document_verified' => 'boolean',
        'driver_license_verified' => 'boolean',
        'approved' => 'boolean',
    ];

    /**
     * The attributes that should be hidden.
     */
    protected $hidden = [
        'id',
        'user_id',
        'identity_document_id',
        'driver_license_id',
    ];
    /**
     * Get the user that owns the application.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the identity document that owns the application.
     */
    public function identityDocument()
    {
        return $this->belongsTo(IdentityDocument::class);
    }

    /**
     * Get the driver license that owns the application.
     */
    public function driverLicense()
    {
        return $this->belongsTo(DriverLicense::class);
    }
}