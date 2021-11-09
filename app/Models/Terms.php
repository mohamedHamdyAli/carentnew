<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Terms
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Terms newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Terms newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Terms query()
 * @method static \Illuminate\Database\Eloquent\Builder|Terms whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Terms whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Terms whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Terms extends Model
{
    use Uuid;
}
