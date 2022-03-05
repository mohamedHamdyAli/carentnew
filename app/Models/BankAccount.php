<?php

namespace App\Models;

use App\Traits\Encryptable;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory, Uuid, Encryptable;

    protected $fillable = [
        'user_id',
        'bank_id',
        'branch_code',
        'account_number',
        'account_name',
        'swift_code',
        'iban',
        'active',
    ];

    protected $encryptable = [
        'account_number',
        'account_name',
        'swift_code',
        'iban',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    protected $hidden = [
        'id',
        'user_id',
        'created_at',
        'updated_at',
        'active',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
