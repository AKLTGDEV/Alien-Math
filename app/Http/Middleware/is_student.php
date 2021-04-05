<?php

namespace App\Http\Middleware;

use Closure;

class is_student
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
        if($request->user->type == "student"){
            return $next($request);
        } else {
            return abort(403);
        }
    }
}
