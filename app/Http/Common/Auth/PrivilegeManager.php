<?php

namespace App\Http\Common\Auth;

use App\Models\Privilege;
use App\Models\UserPrivilege;

class PrivilegeManager
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function grant($privilege)
    {
        if($this->user->hasPrivilege($privilege)) {
            return;
        }

        $privilege_id = Privilege::where('key', $privilege)->first()->id;

        UserPrivilege::create([
            'user_id' => $this->user->id,
            'privilege_id' => $privilege_id,
        ]);
    }

}