<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\File;
use App\Models\Sharedfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SharedFileController extends Controller
{
    public function index()
    {

    }

    public function show()
    {

    }

    public function store($id,$token,$requester_id,$file_id)
    {
        $user = User::where(['id' => $id , 'user-token' => $token])->first() ??
            Admin::where(['id' => $id , 'admin-token' => $token])->first();

        $sharedFile = new Sharedfile();

        if($user instanceof User)
        {
            $sharedFile->file_id = $file_id;
            $sharedFile->user_owner_id = $user->id;
        }
        else
        {
            $sharedFile->file_id = $file_id;
            $sharedFile->admin_owner_id = $user->id;
        }

        $sharedFile->user_requester_id = $requester_id;

        $sharedFile->save();



    }

    /**
     * Download a requested filed
     */
    public function download($id,$token,$fileID,$hashname)
    {
        $user = User::where(['id' => $id , 'user-token' => session()->get('_token')])->first() ??
            Admin::where(['id' => $id , 'admin-token' => session()->get('_token')])->first();

        $file = null;
        if($user instanceof User)
        {
            $file = File::where([
                'id' => $fileID,
                'file' => $hashname,
                'is_current_version' => true])
                ->first();


            // make sure that the file has been shared with the user
            if(count(Sharedfile::where(['file_id' => $fileID,'user_requester_id' => $id])->get()) === 0)
            {
                abort (404);
            }
        }else
        {
            $file = File::where([
                'file' => $hashname,
                'id' => $fileID,
                'is_current_version' => true])
                ->first();

            // make sure that the file has been shared with the user
            if(count(Sharedfile::where(['file_id' => $fileID,'admin_requester_id' => $id])->get()) === 0)
            {
                abort (404);
            }
        }




        $path_extension = pathinfo($file->file, PATHINFO_EXTENSION);

        return Storage::download("files/{$file->file}", $file->filename.'.'.$path_extension);
    }

    public function delete(Request $request,$id,$token,$fileid)
    {
        $file = File::find($fileid)->first();

        $user = User::where(['id' => $id , 'user-token' => $token])->first() ??
            Admin::where(['id' => $id , 'admin-token' => $token])->first();


        $sharedfile = null;

        if($user instanceof User) {
            $sharedfile = Sharedfile::where([
                'file_id' => $fileid,
                'user_requester_id' => $id
            ]);
        }
        else {
            $sharedfile = Sharedfile::where([
                'file_id' => $fileid,
                'admin_requester_id' => $id
            ]);
        }

        $sharedfile->delete();
        return response()->json([$file]);
    }
}
