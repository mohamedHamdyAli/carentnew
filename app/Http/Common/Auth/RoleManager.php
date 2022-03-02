<?php

namespace App\Http\Common\Auth;

use App\Models\Role;
use App\Models\UserRole;

class RoleManager
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Assign a role to the user.
     * @param $role
     * @return void
     */
    public function assign($role)
    {
        if ($this->user->hasRole($role)) {
            return;
        }

        $role_id = Role::where('key', $role)->first()?->id;

        UserRole::updateOrcreate([
            'user_id' => $this->user->id,
            'role_id' => $role_id,
        ]);
    }

    /**
     * Unassign a role from the user.
     * @param $role
     * @return void
     */
    public function unassign($role)
    {
        if (!$this->user->hasRole($role)) {
            return;
        }

        $role_id = Role::where('key', $role)->first()?->id;

        UserRole::where('user_id', $this->user->id)
            ->where('role_id', $role_id)
            ->delete();
    }
}
