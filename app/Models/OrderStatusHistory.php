<?php

namespace App\Models;

use App\Traits\OrderedUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderStatusHistory
 */
class OrderStatusHistory extends Model
{
    use HasFactory, OrderedUuid;

    protected $fillable = [
        'order_id',
        'order_status_id',
        'created_at',
        'updated_at',
    ];

    public function orderStatus()
    {
        return $this->belongsTo(OrderStatus::class);
    }
}
