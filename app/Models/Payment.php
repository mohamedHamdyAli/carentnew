<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, Uuid;
    public $timestamps = false;
    protected $fillable = [
        'type',
        'referenceNumber',
        'merchantRefNumber',
        'orderAmount',
        'paymentAmount',
        'fawryFees',
        'paymentMethod',
        'orderStatus',
        'paymentTime',
        'customerMobile',
        'customerMail',
        'customerProfileId',
        'signature',
        'statusCode',
        'statusDescription',
    ];

    protected $casts = [
        'orderAmount' => 'decimal:2,8',
        'paymentAmount' => 'decimal:2,8',
        'fawryFees' => 'decimal:2,8',
        'statusCode' => 'integer',
        'paymentTime' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
