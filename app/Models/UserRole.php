<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserRole
 *
 * @property string $id
 * @property string $user_id
 * @property string $role_id
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole whereUserId($value)
 * @mixin \Eloquent
 */
class UserRole extends Model
{
    use Uuid;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'role_id',
    ];

    protected $hidden = [
        'id',
        'user_id',
        'role'
    ];

    protected $appends = [
        'role_key'
    ];

    public function getRoleKeyAttribute()
    {
        return $this->role->key;
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
