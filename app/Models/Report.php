<?php

namespace App\Models;

use App\Traits\OrderedUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory, OrderedUuid;
    public $timestamps = false;

    protected $fillable = [
        'sales',
        'bookings',
        'users',
        'vehicles',
        'date',
    ];

    protected $casts = [
        'sales' => 'integer',
        'bookings' => 'integer',
        'users' => 'integer',
        'vehicles' => 'integer',
        'date' => 'date',
    ];

    public function scopeMonthly($query)
    {
        return $query->whereMonth('created_at', now()->month);
    }

    public function scopeYearly($query)
    {
        return $query->whereYear('created_at', now()->year);
    }

    public function scopeDaily($query)
    {
        return $query->whereDay('created_at', now()->day);
    }

    public function scopeWeekly($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }
}
