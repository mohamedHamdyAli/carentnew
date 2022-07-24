<?php

namespace App\Models;

use App\Traits\OrderedUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderPayment
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPayment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Invoice extends Model
{
    use OrderedUuid;
    protected $fillable = [
        'order_id',
        'payment_id',
        'sub_total',
        'vat',
        'discount',
        'total',
        'currency',
    ];

    protected $hidden = [
        'id',
        'updated_at',
    ];

    protected $casts = [
        'sub_total' => 'float',
        'vat' => 'float',
        'discount' => 'float',
        'total' => 'float',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
