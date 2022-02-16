<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CacheHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    public function index()
    {
        // validate request
        $this->validate(request(), [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1',
            'search' => 'sometimes|string',
            'role_ids' => 'sometimes|array',
            'role_ids.*' => 'string|exists:roles,id',
        ]);

        $data = Cache::tags(['users'])->remember(CacheHelper::makeKey('users'), 600, function () {
            $users = User::where('id', '!=', auth()->id());

            // filter by role
            if (request()->has('role_ids')) {
                $users = $users->whereHas(
                    'UserRoles',
                    function ($query) {
                        return $query->whereIn('role_id', request('role_ids'));
                    }
                );
            }

            // search
            if (request()->has('search')) {
                $users = $users->where(function ($query) {
                    return $query->where('name', 'like', '%' . request('search') . '%')
                        ->orWhere('email', 'like', '%' . request('search') . '%')
                        ->orWhere('phone', 'like', '%' . request('search') . '%');
                });
            }

            $users = $users->paginate(request('per_page', 20));

            // add created_at to users
            $users = $users->setCollection($users->getCollection()->map(function ($user) {
                $user->makeVisible(['created_at', 'is_active'])->makeHidden(['language']);
                return $user;
            }));

            return $users;
        });

        return response()->json($data);
    }
}
