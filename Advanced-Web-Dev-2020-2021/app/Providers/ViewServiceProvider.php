<?php

namespace App\Providers;

use App\Models\{Admin, Message, Notification, Problem, User};
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * closure based composer
         * Pass notifications to Layout view
         */
        View::composer('*',function($view){

            $user = User::where(['id' => session()->get('id') , 'user-token' => session()->get('_token')])->first() ??
                Admin::where(['id' => session()->get('id') , 'admin-token' => session()->get('_token')])->first();


            $notification = null;

            $usertype;

            if($user instanceof User)
            {
                $usertype = "User";
                $notification = Notification::where(['user_id' => session()->get('id')] )->take(10)->get();
            }
            else
            {
                $usertype = "Admin";
                $notification = Notification::where(['admin_id' => session()->get('id')] )->take(5)->get();
            }

            $problemIDs = [];


            foreach($notification as $notify) {
                $problemIDs[] = $notify->problem_id;
            }


            $view->with([
                'notifications' => $notification,
                'problem_id' => $problemIDs,
                'profileimage' => $user->picture ?? false,
                'usertype' => $usertype,
            ]);
        });
    }
}
