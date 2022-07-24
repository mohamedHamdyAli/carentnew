<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OwnerApplication extends Model
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
        'approved' => 'boolean',
        'identity_document_verified' => 'boolean',
    ];

    /**
     * The attributes that should be hidden.
     */
    protected $hidden = [
        'id',
        'user_id',
        'identity_document_id',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'identity_document_uploaded',
        'approved',
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
        return $this->hasOne(IdentityDocument::class, 'id', 'identity_document_id');
    }

    public function getIdentityDocumentUploadedAttribute()
    {
        return $this->identityDocument() ? true : false;
    }

    public function getApprovedAttribute()
    {
        return $this->status === 'approved';
    }
}
