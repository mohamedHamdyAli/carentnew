<?php

namespace App\Models;

use App\Traits\OrderedUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderRefund
 *
 * @property string $id
 * @property string $order_id
 * @property string $type
 * @property string $amount
 * @property string|null $approved_at
 * @property string $approved_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrderRefund newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderRefund newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderRefund query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderRefund whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderRefund whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderRefund whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderRefund whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderRefund whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderRefund whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderRefund whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderRefund whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OrderRefund extends Model
{
    use OrderedUuid;
}
