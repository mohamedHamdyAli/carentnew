<?php

namespace App\Models;

use App\Traits\OrderedUuid;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use OrderedUuid;
}
