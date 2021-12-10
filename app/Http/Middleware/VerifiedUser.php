<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifiedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()->isVerified()) {
            return response()->json([
                'message' => __('messages.error.verified'),
                'data' => null,
                'error' => true,
            ], 403);
        }
        return $next($request);
    }
}
