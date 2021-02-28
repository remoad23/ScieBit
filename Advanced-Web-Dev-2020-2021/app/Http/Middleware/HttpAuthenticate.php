<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

/**
 * Class HttpAuthenticate
 * @package App\Http\Middleware
 * Validates the Authenticated User who is requesting HTTP Requests
 */
class HttpAuthenticate
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

        // Wildcards avaible in the route?
        if(isset($request->id) && isset($request->token))
        {
            $id = $request->id;
            $token = $request->token;


            $user = User::where(['id' => $id, 'user-token' => $token])->first() ??
                Admin::where(['id' => $id, 'admin-token' => $token])->first();

            if($user === null) abort(404);

            if($user instanceof User)
            {
                // check if user doesnt exists and if id and token dont match with sessionid and sessiontoken
                if($id !== strval($user->id) || $token !== $user['user-token'])
                {
                    abort(404);
                }
            }
            else if($user instanceof Admin)
            {
                // check if user doesnt exists and if id and token dont match with sessionid and sessiontoken
                if($id !== strval($user->id) || $token !== $user['admin-token'])
                {
                    abort(404);
                }
            }

        }
        else{
            abort(404);
        }
        return $next($request);
    }
}
