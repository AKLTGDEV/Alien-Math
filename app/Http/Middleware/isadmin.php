<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class isadmin
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
        /**
         * We check whether the logged in user is admin ny 2 ways:
         * 
         *  1. If his username belongs in the special admins list
         *  2. If the $user->type atribute is setr to "admin".
         */

        $list = array(
            "admin",
        );

        if(in_array(Auth::user()->username, $list)){
            return $next($request);
        } else {
            //Check if user type is set to be "admin".
            if(Auth::user()->type == "admin"){
                return $next($request);
            }            
        }

        //return $next($request);
        return abort(403);
    }
}
