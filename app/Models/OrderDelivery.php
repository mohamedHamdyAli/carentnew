<?php

namespace App\Models;

use App\Traits\OrderedUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderDelivery
 *
 * @property string $id
 * @property string $order_id
 * @property string|null $owner_delivered_at
 * @property string|null $renter_received_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDelivery newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDelivery newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDelivery query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDelivery whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDelivery whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDelivery whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDelivery whereOwnerDeliveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDelivery whereRenterReceivedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderDelivery whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OrderDelivery extends Model
{
    use OrderedUuid;
}
