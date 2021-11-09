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

    public function assign($role)
    {
        if($this->user->hasRole($role)) {
            return;
        }

        $role_id = Role::where('key', $role)->first()->id;

        UserRole::create([
            'user_id' => $this->user->id,
            'role_id' => $role_id,
        ]);
    }

}