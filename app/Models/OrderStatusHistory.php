<?php

namespace App\Models;

use App\Traits\OrderedUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderStatusHistory
 *
 * @property int $id
 * @property string $order_id
 * @property string $order_status_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatusHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatusHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatusHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatusHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatusHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatusHistory whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatusHistory whereOrderStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderStatusHistory whereUpdatedAt($value)
 * @mixin \Eloquent
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
}
