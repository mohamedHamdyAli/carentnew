<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

class BusinessDocument extends Model
{
    use Uuid;

    protected $fillable = [
        'user_id',
        'name',
        'logo',
        'legal_documents',
    ];

    protected $hidden = [
        'logo',
        'user_id',
        'created_at',
        'updated_at',
        'verified_at',
    ];

    protected $casts = [
        'legal_documents' => 'array',
    ];

    protected $appends = [
        'logo_url',
    ];

    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
