<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class Locale
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
        $lang = $request->header('Language');
        $request->headers->set('Language', strtolower($lang));
        $lang = in_array($lang, ['en', 'ar']) ? $lang : config('app.locale');
        App::setLocale($lang);
        return $next($request);
    }
}
