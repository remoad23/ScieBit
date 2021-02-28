<?php
namespace App\Http\Controllers\AuthControllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use function PHPUnit\Framework\isEmpty;


class RegistrationController extends Controller
{

    public function index()
    {
        return view('Auth.Register.index');
    }

    protected function store(Request $request)
    {

        $request->validate([
            'firstname' => 'required|alpha|max:200',
            'lastname' => 'required|alpha|max:200',
            'email' => 'required|email:rfc|unique:user|unique:admin',
            'password' => 'required|required_with:password_confirmation|same:verifypassword|max:50',
            'verifypassword' => 'required|max:50',
        ]);

        if(count(User::where('email' , $request['email'])->get()) > 0 ||
            $request->password !== $request->verifypassword){

            return back()->withErrors('ErrorMessage','Error has occured');
        }

        $user = User::create([
            'name' => $request['firstname'],
            'lastname' => $request['lastname'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'api_token' => Str::random(60),
        ]);
        $user->save();
        return back();
    }

}
