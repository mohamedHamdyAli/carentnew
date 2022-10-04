<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CacheHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserPrivilege;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rules\Password;

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
            $users = User::with('allRoles')->where('id', '!=', auth()->id());

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

    public function store()
    {

        $password = request('password');
        $id = request('id');

        $emailUnique = ($id && $id != '') ? 'unique:users,email,' . $id : 'unique:users,email';
        $phoneUnique = ($id && $id != '') ? 'unique:users,phone,' . $id : 'unique:users,phone';
        // validate request
        $this->validate(request(), [
            'id'        => ['sometimes', 'nullable', 'string', 'exists:users,id'],
            'name'      => ['sometimes', 'regex:/^(?!.*\d)[أ-يa-z\s]{2,66}$/iu'], // * Name without numbers
            'phone'     => ['sometimes', $phoneUnique, 'regex:/^(\+)[0-9]{10,15}$/'], // * International phone number
            'email'     => ['sometimes', $emailUnique, 'email'], // * Unique email address
            'password'  => ['sometimes', 'nullable', Password::min(8)->letters()->numbers()], // * Strong password
            'is_active' => ['sometimes', 'boolean'], // * Boolean
        ]);
        $data = request()->only(['name', 'phone', 'email', 'is_active']);

        return DB::transaction(function () use ($data, $password, $id) {
            if ($password && $password != '') {
                $data['password'] = bcrypt($password);
            }

            if ($id && $id != '') {
                $data['id'] = $id;
                $user = User::find($id);
                $user->update($data);
            } else {
                $user = User::create($data);
                $user->assignRole('admin');
            }

            if (request()->has('privileges')) {
                $privileges = request('privileges');
                UserPrivilege::where('user_id', $user->id)
                    ->whereNotIn('privilege_id', $privileges)
                    ->delete();
                foreach ($privileges as $privilege) {
                    UserPrivilege::create(
                        ['user_id' => $user->id, 'privilege_id' => $privilege]
                    );
                }
            }

            Cache::tags(['vehicles', 'users'])->flush();

            return $user;
        });
    }

    public function show($id)
    {
        $user = User::with('BankAccount')->findOrFail($id)->makeVisible(['is_active']);
        $privilages = UserPrivilege::where('user_id', $id)->pluck('privilege_id');
        $user = $user->toArray();
        $user['privileges'] = $privilages;
        return response()->json($user);
    }
}
