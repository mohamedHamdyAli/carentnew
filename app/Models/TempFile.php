<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

class TempFile extends Model
{
    use Uuid;

    public $timestamps = false;
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $fillable = [
        'path',
        'name',
        'mime',
        'size',
        'uploaded_at',
    ];
}
