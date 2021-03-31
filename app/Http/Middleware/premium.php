<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class premium
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user()->isPremium()) {
            // The user is a premium Member
            return $next($request);
        } else {
            // The user is not a premium Member
            return redirect()->route("premium_index");
        }
    }
}
