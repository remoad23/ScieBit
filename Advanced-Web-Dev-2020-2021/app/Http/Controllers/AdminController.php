<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Departmentadmin;
use App\Models\Departmentuser;
use App\Models\Sharedfile;
use App\Models\Sharedfolder;
use App\Models\User;
use App\Models\Message;
use App\Models\Notification;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function delete($id)
    {
        $user = Admin::find($id);

        $user->delete();
        return redirect()->back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * change a Admin entity to a User entity
     */
    public function switchToUser($id)
    {
        $user = Admin::where('id',$id)->first();

        $files = $user->file()->get();
        $folders = $user->folder()->get();
        $sharedfiles = $user->sharedfile()->get();
        $sharedfolders = $user->sharedfolder()->get();
        $sharedFilesBelonged = Sharedfile::where(['admin_owner_id' => $id])->get();
        $sharedFoldersBelonged = Sharedfolder::where(['admin_owner_id' => $id])->get();
        $departmentUser = Departmentadmin::where('admin_id',$id)->get();
        $messages = Message::where('writer_admin',$id)->get();
        $notifications = Notification::where('admin_id',$id)->get();


        $admin = new User();
        $admin->name = $user->name;
        $admin->lastname = $user->lastname;
        // email will be set at the end of method
        $admin->email = "placeholder@gmail.com";
        $admin->password = $user->password;
        $admin->picture = $user->picture;
        $admin['user-token'] = $user['admin-token'];
        $admin->save();

        // change files/folder/department dependency from current user to new admin entity

        foreach($files as $file)
        {
            $file->admin_owner_id = null;
            $file->user_owner_id = $admin->id;
            $file->save();
        }

        foreach($folders as $folder)
        {
            $folder->admin_owner_id = null;
            $folder->user_owner_id = $admin->id;
            $folder->save();
        }

        foreach($sharedfiles as $sharedfile)
        {
            $sharedfile->admin_requester_id = null;
            $sharedfile->user_requester_id = $admin->id;
            $sharedfile->save();
        }

        foreach($sharedfolders as $sharedfolder)
        {
            $sharedfolder->admin_requester_id = null;
            $sharedfolder->user_requester_id = $admin->id;
            $sharedfolder->save();
        }

        foreach($sharedFilesBelonged as $sharedFileBelonged)
        {
            $sharedFileBelonged->admin_owner_id = null;
            $sharedFileBelonged->user_owner_id = $admin->id;
            $sharedFileBelonged->save();
        }

        foreach($sharedFoldersBelonged as $sharedFolderBelonged)
        {
            $sharedFolderBelonged->admin_owner_id = null;
            $sharedFolderBelonged->user_owner_id = $admin->id;
            $sharedFolderBelonged->save();
        }

        foreach($departmentUser as $duser)
        {
            $departmentAdmin = new Departmentuser();
            $departmentAdmin->department_id = $duser->department_id;
            $departmentAdmin->user_id = $admin->id;
            $departmentAdmin->save();
            $duser->delete();
        }
        foreach($messages as $message){
            $message->writer_user = $admin->id;
            $message->writer_admin = null;
            $message->save();
        }
        foreach($notifications as $notification){
            $notification->user_id = $admin->id;
            $notification->admin_id = null;
            $notification->save();
        }


        $copyuser = (clone $user);
        $user->delete();
        $admin->email = $copyuser->email;
        $admin->save();

        return redirect()->back();
    }
}
