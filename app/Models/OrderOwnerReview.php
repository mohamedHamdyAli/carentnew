<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderOwnerReview
 *
 * @property int $id
 * @property string $order_id
 * @property string $renter_id
 * @property int $cleaness_rate
 * @property int $condition_rate
 * @property int $communication_rate
 * @property int|null $overall_rate
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\OrderOwnerReviewFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderOwnerReview newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderOwnerReview newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderOwnerReview query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderOwnerReview whereCleanessRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderOwnerReview whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderOwnerReview whereCommunicationRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderOwnerReview whereConditionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderOwnerReview whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderOwnerReview whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderOwnerReview whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderOwnerReview whereOverallRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderOwnerReview whereRenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderOwnerReview whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OrderOwnerReview extends Model
{
    use HasFactory;
}
