<?php

namespace App\Models;

use App\Traits\OrderedUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RewardPointTransaction
 *
 * @property string $id
 * @property string $user_id
 * @property int $points
 * @property string $operation
 * @property string $type
 * @property string|null $order_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|RewardPointTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RewardPointTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RewardPointTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|RewardPointTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RewardPointTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RewardPointTransaction whereOperation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RewardPointTransaction whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RewardPointTransaction wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RewardPointTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RewardPointTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RewardPointTransaction whereUserId($value)
 * @mixin \Eloquent
 */
class RewardPointTransaction extends Model
{
    use OrderedUuid;
}
