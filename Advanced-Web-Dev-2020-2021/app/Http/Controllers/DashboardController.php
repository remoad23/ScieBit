<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    public function index()
    {
        $notifications = Notification::where(['user_id'=> session()->get('id')])->get();
        $user = Admin::where(['admin-token' => session()->get('_token'), 'id' => session()->get('id')])->first() ??
                    User::where(['user-token' => session()->get('_token'), 'id' => session()->get('id')])->first();

        if($user instanceof User){
            $user->admin = false;
        }
        else{
            $user->admin = true;
        }

        return view('index',['notifications' => $notifications, 'user' => $user]);
    }
}
