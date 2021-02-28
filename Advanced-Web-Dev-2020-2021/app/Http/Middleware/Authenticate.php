<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use Closure;

class Authenticate extends Middleware
{

    /**
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|mixed
     * Authentication for Admin/User inside Application for general purposes
     */
    public function handle(Request $request,Closure $next)
    {

       if(auth()->guard('user')->check($request) || auth()->guard('admin')->check($request))
       {
           return $next($request);

       }
       else{

           return redirect('Login');
       }
    }
}
