<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Admin;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
        $this->registerPolicies();

        // User Closure-Request Guard for User
        Auth::viaRequest('user-token', function ($request)
        {
            return User::where([
                'user-token' => session()->get('_token'),
                'id' => session()->get('id')])
                ->first();
        });

        // Closure-Request Guard for Admin
        Auth::viaRequest('admin-token', function ($request)
        {
            return Admin::where([
                'admin-token' => session()->get('_token'),
                'id' => session()->get('id')])
                ->first();
        });

    }

    /**
     * Used to see if authenticated user is really the userid that has been sent through http requests
     * @param $id
     * @param $token
     * @return bool
     */
    public static function verifyUser($id,$token)
    {
        return request()->session()->get('_token') === $token &&
            strval(request()->session()->get('id')) === $id;
    }
}
