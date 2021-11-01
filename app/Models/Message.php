<?php

namespace App\Models;

use App\Http\Traits\OrderedUuid;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use OrderedUuid;
}
