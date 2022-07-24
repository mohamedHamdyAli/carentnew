<?php

namespace App\Models;

use App\Traits\OrderedUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderExtention
 *
 * @method static \Illuminate\Database\Eloquent\Builder|OrderExtention newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderExtention newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderExtention query()
 * @mixin \Eloquent
 */
class OrderExtention extends Model
{
    use OrderedUuid;
}
