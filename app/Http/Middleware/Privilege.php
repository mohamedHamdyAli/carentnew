<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Privilege
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $privilege)
    {
        if (!$request->user()->hasPrivilege($privilege)) {
            return response([
                'message' => __('messages.error.forbidden'),
                'data' => null,
                'error' => true,
            ], 403);
        }

        return $next($request);
    }
}
