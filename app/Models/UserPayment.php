<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserPayment
 *
 * @property string $id
 * @property string $user_id
 * @property string $amount
 * @property string $attachments
 * @property string $details
 * @property int $sent
 * @property string $created_by
 * @property string $approved_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPayment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPayment whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPayment whereAttachments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPayment whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPayment whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPayment whereSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPayment whereUserId($value)
 * @mixin \Eloquent
 */
class UserPayment extends Model
{
    use Uuid;
}
