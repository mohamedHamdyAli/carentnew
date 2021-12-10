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

    /**
     * Grant a privilege to user.
     * @param $privilege
     * @return void
     */
    public function grant($privilege)
    {
        if ($this->user->hasPrivilege($privilege)) {
            return;
        }

        $privilege_id = Privilege::where('key', $privilege)->first()->id;

        UserPrivilege::create([
            'user_id' => $this->user->id,
            'privilege_id' => $privilege_id,
        ]);
    }

    /**
     * Revoke a privilege from the user.
     * @param $privilege
     * @return void
     */
    public function revoke($privilege)
    {
        if (!$this->user->hasPrivilege($privilege)) {
            return;
        }

        $privilege_id = Privilege::where('key', $privilege)->first()->id;

        UserPrivilege::where('user_id', $this->user->id)
            ->where('privilege_id', $privilege_id)
            ->delete();
    }
}
