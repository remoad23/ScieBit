<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Departmentadmin;
use App\Models\Departmentuser;
use App\Models\File;
use App\Models\Folder;
use App\Models\Sharedfile;
use App\Models\Sharedfolder;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Problem;
use App\Models\User;
use App\Providers\AuthServiceProvider as MyAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;

class UserController extends Controller
{
    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * Show curred logged in User
     */
    public function index($id)
    {
        $user = Admin::where(['admin-token' => session()->get('_token'), 'id' => $id])->first() ??
            User::where(['user-token' => session()->get('_token'), 'id' => $id])->first();

        return View('Users.User.index',['user' => $user]);
    }

    /**
     * Authorize User in Drive by verifying if user token is valid
     */
    public function show($id,$token)
    {
        $user = Admin::where(['admin-token' => $token, 'id' => $id])->first() ??
            User::where(['user-token' => $token, 'id' => $id])->first();

        $usertype = $user instanceof Admin ? "Admin" : "User";

        return response()->json([
            'username' => $user->name . " " . $user->lastname,
            'valid' => true,
            'type' => $usertype,
        ]);
    }

    public function delete($id)
    {
        $user = User::find($id);

        $user->delete();
        return redirect()->back();
    }

    public function redirectToDrive(Request $request)
    {
        $user = Admin::where('admin-token', $request->session()->get('_token'))->first() ??
            User::where('user-token', $request->session()->get('_token'))->first();

        $token = $user['admin-token'] ?? $user['user-token'];
        $id = $user->id;

        if (App::isLocale('en')) {
            return redirect()->away("http://localhost:4200/document?token={$token}&id={$id}");
        }
        else{
            return redirect()->away("http://localhost:4201/document?token={$token}&id={$id}");
        }


    }

    public function redirectToSharedfile(Request $request){
        $user = Admin::where('admin-token', $request->session()->get('_token'))->first() ??
            User::where('user-token', $request->session()->get('_token'))->first();

        $token = $user['admin-token'] ?? $user['user-token'];
        $id = $user->id;

        if (App::isLocale('en')) {
            return redirect()->away("http://localhost:4200/document?token={$token}&id={$id}&shared=1");
        }
        else{
            return redirect()->away("http://localhost:4201/document?token={$token}&id={$id}&shared=1");
        }

    }

    public function queryUser($id,$token,$query,$usertype)
    {

        $user = null;
        //user
        if($usertype === "1")
        {
            $user = User::select('id','email')->where('email', 'LIKE', "%$query%")->orderBy('email', 'asc')->get();
        }
        //admin
        else if($usertype === "2"){
            if($user instanceof User) abort(404);
            $user = Admin::select('id','email')->where('email', 'LIKE', "%$query%")->orderBy('email', 'asc')->get();
        }



        $user = !($user->count() > 0) ? "NotFound" : $user;
        return response()->json([$user]);
    }

    public function getUserType(Request $request,$id,$token)
    {
            $user = Admin::where(['admin-token' => $token, 'id' => $id])->first() ??
                User::where(['user-token' => $token, 'id' => $id])->first();

            if($user instanceof User)
            {
                return response()->json("User");
            }
            else if ($user instanceof Admin)
            {
                return response()->json("Admin");
            }
            else
            {
                return abort(404);
            }
    }

    public function queryDepartmentUser($query)
    {
        //$user = User::where('username', 'like', "%${$query}%");
       // return response()->json([$user]);
    }

    public function rights($id)
    {

    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * change a User entity to a Admin entity
     */
    public function switchToAdmin($id)
    {
        $user = User::where('id',$id)->first();

        $files = $user->file()->get();
        $folders = $user->folder()->get();
        $sharedfiles = $user->sharedfile()->get();
        $sharedfolders = $user->sharedfolder()->get();
        $sharedFilesBelonged = Sharedfile::where(['user_owner_id' => $id])->get();
        $sharedFoldersBelonged = Sharedfolder::where(['user_owner_id' => $id])->get();
        $departmentUser = Departmentuser::where('user_id',$id)->get();
        $problems = Problem::where('author',$id)->get();
        $notifications = Notification::where('admin_id',$id)->get();
        //$messages = Message::where('writer_user',$id)->get();

        $admin = new Admin();
        $admin->name = $user->name;
        $admin->lastname = $user->lastname;
        // email will be set at the end of method
        $admin->email = "placeholder@gmail.com";
        $admin->password = $user->password;
        $admin->picture = $user->picture;
        $admin['admin-token'] = $user['user-token'];
        $admin->save();

        // change files/folder/department dependency from current user to new admin entity

        foreach($files as $file)
        {
            $file->admin_owner_id = $admin->id;
            $file->user_owner_id = null;
            $file->save();
        }

        foreach($folders as $folder)
        {
            $folder->admin_owner_id = $admin->id;
            $folder->user_owner_id = null;
            $folder->save();
        }

        foreach($sharedfiles as $sharedfile)
        {
            $sharedfile->admin_requester_id = $admin->id;
            $sharedfile->user_requester_id = null;
            $sharedfile->save();
        }

        foreach($sharedfolders as $sharedfolder)
        {
            $sharedfolder->admin_requester_id = $admin->id;
            $sharedfolder->user_requester_id = null;
            $sharedfolder->save();
        }

        foreach($sharedFilesBelonged as $sharedFileBelonged)
        {
            $sharedFileBelonged->admin_owner_id = $admin->id;
            $sharedFileBelonged->user_owner_id = null;
            $sharedFileBelonged->save();
        }

        foreach($sharedFoldersBelonged as $sharedFolderBelonged)
        {
            $sharedFolderBelonged->admin_owner_id = $admin->id;
            $sharedFolderBelonged->user_owner_id = null;
            $sharedFolderBelonged->save();
        }

        foreach($departmentUser as $duser)
        {
            $departmentAdmin = new Departmentadmin();
            $departmentAdmin->department_id = $duser->department_id;
            $departmentAdmin->admin_id = $admin->id;
            $departmentAdmin->save();
            $duser->delete();
        }

        foreach($problems as $problem){
            $problem->delete();
        }

        foreach($notifications as $notification){
            $notification->admin_id = $admin->id;
            $notification->user_id = null;
            $notification->save();
        }

        /*foreach($messages as $message){
            $message->writer_user = null;
            $message->writer_admin = $admin->id;
            $message->save();
        }*/


        $copyuser = (clone $user);
        $user->delete();
        $admin->email = $copyuser->email;
        $admin->save();

        return redirect()->back();
    }
}
