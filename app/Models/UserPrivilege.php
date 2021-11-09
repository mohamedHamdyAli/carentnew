<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserPrivilege
 *
 * @property string $id
 * @property string $user_id
 * @property string $privilege_id
 * @method static \Illuminate\Database\Eloquent\Builder|UserPrivilege newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPrivilege newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPrivilege query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPrivilege whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPrivilege wherePrivilegeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPrivilege whereUserId($value)
 * @mixin \Eloquent
 */
class UserPrivilege extends Model
{
    use Uuid;
}
