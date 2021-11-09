<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vehicle
 *
 * @property string $id
 * @property string $user_id
 * @property string $country_id
 * @property string $state_id
 * @property string|null $category_id
 * @property string|null $brand_id
 * @property string|null $model_id
 * @property string|null $plate_number
 * @property string|null $manufacture_year
 * @property string|null $color
 * @property string|null $fuel
 * @property string|null $features
 * @property int|null $seat_count
 * @property int|null $rating
 * @property int $views
 * @property int $rented
 * @property int $active
 * @property string|null $inactive_message
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\VehicleFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle query()
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereFeatures($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereFuel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereInactiveMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereManufactureYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle wherePlateNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereRented($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereSeatCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vehicle whereViews($value)
 * @mixin \Eloquent
 */
class Vehicle extends Model
{
    use HasFactory, Uuid;
}
