<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AnyRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $roles)
    {
        $roles = explode('|', $roles);
        // grant access on the first role match

        foreach ($roles as $role) {
            if ($request->user()->hasRole($role)) {
                return $next($request);
            }
        }

        return response([
            'message' => __('messages.error.forbidden'),
            'data' => null,
            'error' => true,
        ], 403);
    }
}
