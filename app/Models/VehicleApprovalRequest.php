<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\VehicleApprovalRequest
 *
 * @property int $id
 * @property string $vehicle_id
 * @property int|null $passed
 * @property string|null $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleApprovalRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleApprovalRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleApprovalRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleApprovalRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleApprovalRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleApprovalRequest whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleApprovalRequest wherePassed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleApprovalRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VehicleApprovalRequest whereVehicleId($value)
 * @mixin \Eloquent
 */
class VehicleApprovalRequest extends Model
{
    use HasFactory;
}
