<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

class AgencyApplication extends Model
{
    use Uuid;

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
        'business_document_id',
        'business_document_verified',
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
        'identity_document_verified' => 'boolean',
        'business_document_verified' => 'boolean',
        'approved' => 'boolean',
    ];

    /**
     * The attributes that should be hidden.
     */
    protected $hidden = [
        'id',
        'user_id',
        'identity_document_id',
        'business_document_id',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'identity_document_uploaded',
        'business_document_uploaded',
        'approved',
    ];

    /**
     * Get the user that owns the application.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getApprovedAttribute()
    {
        return $this->status === 'approved';
    }

    /**
     * Get the identity document that owns the application.
     */
    public function identityDocument()
    {
        return $this->hasMany(IdentityDocument::class, 'user_id', 'user_id');
    }

    public function getIdentityDocumentUploadedAttribute()
    {
        return $this->identityDocument()
            ->orderBy('created_at', 'desc')
            ->first([
                'id', 'user_id', 'verified_at', 'created_at'
            ]) ? true : false;
    }

    /**
     * Get the business document that owns the application.
     */
    public function businessDocument()
    {
        return $this->hasMany(BusinessDocument::class, 'user_id', 'user_id');
    }

    public function getBusinessDocumentUploadedAttribute()
    {
        return $this->businessDocument()
            ->orderBy('created_at', 'desc')
            ->first([
                'id', 'user_id', 'verified_at', 'created_at'
            ]) ? true : false;
    }
}
