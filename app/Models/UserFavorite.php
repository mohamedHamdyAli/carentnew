<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserFavorite
 *
 * @property string $id
 * @property string $user_id
 * @property string|null $vehicle_list
 * @method static \Illuminate\Database\Eloquent\Builder|UserFavorite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserFavorite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserFavorite query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserFavorite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFavorite whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFavorite whereVehicleList($value)
 * @mixin \Eloquent
 */
class UserFavorite extends Model
{
    use Uuid;
}
