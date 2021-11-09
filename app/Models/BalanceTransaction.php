<?php

namespace App\Models;

use App\Traits\OrderedUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BalanceTransaction
 *
 * @property string $id
 * @property string $user_id
 * @property string $amount
 * @property string $operation
 * @property string $type
 * @property string|null $order_id
 * @property string|null $refund_id
 * @property string|null $user_payment_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|BalanceTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BalanceTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BalanceTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|BalanceTransaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BalanceTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BalanceTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BalanceTransaction whereOperation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BalanceTransaction whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BalanceTransaction whereRefundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BalanceTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BalanceTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BalanceTransaction whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BalanceTransaction whereUserPaymentId($value)
 * @mixin \Eloquent
 */
class BalanceTransaction extends Model
{
    use HasFactory, OrderedUuid;
}
