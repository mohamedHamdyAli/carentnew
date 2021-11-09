<?php

namespace App\Models;

use App\Traits\OrderedUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Order
 *
 * @property string $id
 * @property string $renter_id
 * @property string $vehicle_id
 * @property string $owner_id
 * @property string $order_Status_id
 * @property string $type
 * @property string|null $extended_from_id
 * @property string $start_date
 * @property string $end_date
 * @property int $day_count
 * @property string $day_cost
 * @property string $total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDayCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDayCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereExtendedFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOrderStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereVehicleId($value)
 * @mixin \Eloquent
 */
class Order extends Model
{
    use OrderedUuid;
}
