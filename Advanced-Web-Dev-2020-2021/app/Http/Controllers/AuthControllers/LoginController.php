<?php

namespace App\Http\Controllers\AuthControllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Lang;
use Session;


class LoginController extends Controller
{


    public function index(Request $request)
    {
        return view('Auth.Login.index');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View View to dashboard or Login
     * Adds new token to logged in User,if User has been found in database
     */
    public function login(Request $request)
    {
        /*$request->validate([
            'email' => 'required|email:rfc',
            'password' => 'required|max:50',
        ]);*/

        $validator = Validator::make($request->all(), [
            'email' => 'required|email:rfc',
            'password' => 'required|max:50',
        ]);

        if ($validator->fails()) {
            return back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $user = User::where(['email' => $request->email])->first() ??
            Admin::where(['email' => $request->email])->first() ?? "notFound";

        //If there is no User then the login view will be displayed
        //otherwise user will be redirected to dashboard
        if(!($user == "notFound") && Hash::check($request->password, $user->password))
        {
            $token = (get_class($user) === Admin::class) ? "admin-token" : "user-token";
            $user->update([$token => $request->session()->get('_token')]);
            $user->save();
            session(['username' => $user->name]);
            session(['lastname' => $user->lastname]);
            session(['id' => $user->id]);
            return redirect('/');
        }
        else
        {
            $validator->errors()->add('failed', Lang::get('auth.failed'));
            return back()->withErrors($validator)->withInput();
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View View to Login
     * Deletes token (setting to null) and redirects to login page
     */
    public function logout(Request $request)
    {
        $tokenString =  $request->session()->get('_token');
        //verify if a user/admin has the same token  inside db as the session object
        $user = User::where(['user-token' => $tokenString])->first() ??
            Admin::where(['admin-token' => $tokenString])->first();

        $token = (get_class($user) === Admin::class) ? "admin-token" : "user-token";
        $user->update([$token => null]);
        //reset Session
        Session::flush();
        return redirect('/Login');
    }



}
