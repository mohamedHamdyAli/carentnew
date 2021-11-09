<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderRenterReview
 *
 * @property int $id
 * @property string $order_id
 * @property string $vehicle_id
 * @property int|null $driver_rate
 * @property int $cleaness_rate
 * @property int $condition_rate
 * @property int $communication_rate
 * @property int|null $overall_rate
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\OrderRenterReviewFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderRenterReview newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderRenterReview newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderRenterReview query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderRenterReview whereCleanessRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderRenterReview whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderRenterReview whereCommunicationRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderRenterReview whereConditionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderRenterReview whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderRenterReview whereDriverRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderRenterReview whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderRenterReview whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderRenterReview whereOverallRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderRenterReview whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderRenterReview whereVehicleId($value)
 * @mixin \Eloquent
 */
class OrderRenterReview extends Model
{
    use HasFactory;
}
