<?php

namespace App\Http\Middleware;

use App;
use Closure;
use Illuminate\Http\Request;

class SetLang
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->lang) {
            App::setLocale(auth()->user()->lang);
        }
        return $next($request);
    }
}
