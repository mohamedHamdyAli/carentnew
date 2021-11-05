<?php

namespace App\Models;

use App\Traits\OrderedUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceTransaction extends Model
{
    use HasFactory, OrderedUuid;
}
