<?php

namespace App\Http\Controllers\Drive;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Notification;
use App\Models\Sharedfile;
use App\Models\Sharedfolder;
use App\Models\Folder;
use App\Models\User;
use App\Models\File;
use App\Models\Keyword;
use Illuminate\Http\Request;

class SharedDocumentController extends Controller
{
    public function index($id,$token)
    {
        //get current user/admin
        $user = User::where(['id' => $id , 'user-token' => $token])->first() ??
            Admin::where(['id' => $id , 'admin-token' => $token])->first();

        $sharedfiles = null;
        $files = [];

        // to make sure its not getting the files where user_requester_id or admin === null
        if($user->id === null) abort(404);

        // get files of current user/admin
        if($user instanceof User)
        {
            $sharedfiles = Sharedfile::where('user_requester_id',$user->id)->get();
            foreach($sharedfiles as $shared)
            {
              $files[] = $shared->file()->first();
            }
        }
        else
        {
            $sharedfiles = Sharedfile::where('admin_requester_id',$user->id)->get();
            foreach($sharedfiles as $shared)
            {
                $files[] = $shared->file()->first();
            }
        }
        if($files != null){
            for($i = 0; $i < sizeOf($files); $i++){
                $files[$i]->filename = File::where('id',$files[$i]->id)->value('filename');
                $files[$i]->keywords = Keyword::where('file_id',$files[$i]->id)->pluck('keyword');
                if($files[$i]->user_owner_id != null){
                    $fileuser = User::where('id', $files[$i]->user_owner_id)->first(['name','lastname']);
                    $files[$i]->ownername = $fileuser->name.' '.$fileuser->lastname;
                }
                else if($files[$i]->admin_owner_id != null){
                    $fileuser = Admin::where('id', $files[$i]->admin_owner_id)->first(['name','lastname']);
                    $files[$i]->ownername = $fileuser->name.' '.$fileuser->lastname;
                }
            }
        }


        return response()->json([$files]);
    }

    public function show()
    {

    }

    public function update()
    {

    }

    public function store(Request $request,$id,$token,$requester_id,$file_id)
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

        $notification = new Notification();
        $notification->type = "sharedfile";
        $notification->file_id = $file_id;

        if($request->userType === "Admin")
        {
            $sharedFile->admin_requester_id = $requester_id;
            $notification->admin_id = $requester_id;
        }
        elseif($request->userType === "User")
        {
            $sharedFile->user_requester_id = $requester_id;
            $notification->user_id = $requester_id;
        }

        $sharedFile->save();

        $notification->save();

    }

    public function delete($id)
    {

    }

    public function indexAll($id,$token)
    {
        //get current user/admin
        $user = User::where(['id' => $id , 'user-token' => $token])->first() ??
            Admin::where(['id' => $id , 'admin-token' => $token])->first();

        $sharedfiles = null;
        $sharedFolders = null;
        $files = [];

        // to make sure its not getting the files where user_requester_id or admin === null
        if($user->id === null) abort(404);

        // get files of current user/admin
        if($user instanceof User)
        {
            $sharedfiles = Sharedfile::where('user_requester_id',$user->id)->get();
            foreach($sharedfiles as $shared)
            {
              $files[] = $shared->file()->first();
            }
            $sharedFolders = Sharedfolder::where('user_requester_id',$user->id)->get();
            foreach($sharedFolders as $folder)
            {
                $this->getChildFiles($files, $folder->folder_id);
            }
        }
        else
        {
            $sharedfiles = Sharedfile::where('admin_requester_id',$user->id)->get();
            foreach($sharedfiles as $shared)
            {
                $files[] = $shared->file()->first();
            }
            $sharedFolders = Sharedfolder::where('admin_requester_id',$user->id)->get();
            foreach($sharedFolders as $folder)
            {
                $this->getChildFiles($files, $folder->folder_id);
            }
        }
        if($files != null){
            for($i = 0; $i < sizeOf($files); $i++){
                $files[$i]->filename = File::where('id',$files[$i]->id)->value('filename');
                $files[$i]->keywords = Keyword::where('file_id',$files[$i]->id)->pluck('keyword');
                if($files[$i]->user_owner_id != null){
                    $fileuser = User::where('id', $files[$i]->user_owner_id)->first(['name','lastname']);
                    $files[$i]->ownername = $fileuser->name.' '.$fileuser->lastname;
                }
                else if($files[$i]->admin_owner_id != null){
                    $fileuser = Admin::where('id', $files[$i]->admin_owner_id)->first(['name','lastname']);
                    $files[$i]->ownername = $fileuser->name.' '.$fileuser->lastname;
                }
            }
        }


        return response()->json([$files]);
    }

    public function getChildFiles(&$files, $folderId)
    {
        $folderFiles = File::where('parentfolder_id', $folderId)->get();
        foreach($folderFiles as $folderFile){
            $files[] = $folderFile;
        }
        $childFolders = Folder::where('parentfolder_id',$folderId)->get();
        if(sizeOf($childFolders) == 0){
            return $files;
        }
        foreach($childFolders as $childFolder){
            $this->getChildFiles($files, $childFolder->id);
        }
    }
}
