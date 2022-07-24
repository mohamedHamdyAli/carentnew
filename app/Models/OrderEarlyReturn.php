<?php

namespace App\Models;

use App\Traits\OrderedUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderEarlyReturn extends Model
{
    use HasFactory, OrderedUuid;

    protected $fillable = [
        'order_id',
        'original_end_date',
        'refunded',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'refunded' => 'boolean',
    ];

    public function scopeRefunded($query)
    {
        return $query->where('refunded', true);
    }

    public function scopeNotRefunded($query)
    {
        return $query->where('refunded', false);
    }
}
