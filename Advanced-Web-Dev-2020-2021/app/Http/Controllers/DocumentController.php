<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Departmentuser;
use App\Models\Sharedfile;
use Illuminate\Http\Request;
use App\Models\{File, Folder, Sharedfolder, User, Admin};
use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

/**
 * Class DocumentController
 * @package App\Http\Controllers
 *
 * HTTP Responses to Angular will be here constructed for the Drive
 */
class DocumentController extends Controller
{
    public function index()
    {
        return view('Document.document.index');
    }

    public function myDocument($id,$token)
    {
        //get current user/admin
        $user = User::where(['id' => $id , 'user-token' => $token])->first() ??
            Admin::where(['id' => $id , 'admin-token' => $token])->first();

        //get files of current admin/user
        if($user instanceof User)
        {
            $files = File::where('user_owner_id',$user->id)->get();
        }
        else
        {
            $files = File::where('admin_owner_id',$user->id)->get();
        }

        return response()->json([
            $files
        ]);

    }

    public function sharedDocument($id,$token)
    {
        //get current user/admin
        $user = $files = User::where(['id' => $id , 'user-token' => $token])->first() ??
            Admin::where(['id' => $id , 'admin-token' => $token])->first();

        $files;

        // to make sure its not getting the files where user_requester_id or admin === null
        if($user->id === null) abort(404);

        // get files of current user/admin
        if($user instanceof User)
        {
            $files = Sharedfile::where('user_requester_id',$user->id)->get();
        }
        else
        {
            $files = Sharedfile::where('admin_requester_id',$user->id)->get();
        }

        return response()->json([
            $files
        ]);
    }

    public function departmentDocument($id)
    {
        $files = User::find($id)->departmentdocument;
        $departments = Departmentuser::find()->where('user_id' , $id )->get();


        return response()->json([
            $files,$departments
        ]);
    }

    public function getFolderPath($id,$token,$fileid)
    {
        $folder = Folder::where('id',$fileid)->first();
        $folderPaths = [];

        // stop when no parentfolder_id can be find for the current folder
        for([$currentFolder,$x] = [$folder,0]; ;
            [$currentFolder,$x] = [Folder::where('id',$currentFolder->parentfolder_id)->first(),$x+1])
        {
            $folderPaths[$x] = [$currentFolder->id,$currentFolder->foldername];
            if(is_null($currentFolder->parentfolder_id)) break;
        }

        return response()->json($folderPaths);

    }

    public function getSharedFolderPath($id,$token,$fileid)
    {
        $user = $files = User::where(['id' => $id , 'user-token' => $token])->first() ??
            Admin::where(['id' => $id , 'admin-token' => $token])->first();

        $folder = Folder::where('id',$fileid)->first();
        $folderPaths = [];
        $folderPaths[] = [$folder->id,$folder->foldername];

        //get files of current admin/user
        if($user instanceof User)
        {
            // stop when no parentfolder_id can be find for the current folder
            for([$currentFolder,$x] = [$folder,0]; ;
            [$currentFolder,$x] = [Folder::where('id',$currentFolder->parentfolder_id)->first(),$x+1])
            {
                $folderPaths[$x] = [$currentFolder->id,$currentFolder->foldername];
                if(Sharedfolder::where(['user_requester_id' => $id,'folder_id' =>$currentFolder->id])->first() !== null) break;
                if(is_null($currentFolder->parentfolder_id)) break;
            }
        }
        else
        {
            // stop when no parentfolder_id can be find for the current folder
            for([$currentFolder,$x] = [$folder,0]; ;
            [$currentFolder,$x] = [Folder::where('id',$currentFolder->parentfolder_id)->first(),$x+1])
            {
                $folderPaths[$x] = [$currentFolder->id,$currentFolder->foldername];
                if(Sharedfolder::where(['admin_requester_id' => $id,'folder_id' => $currentFolder->id])->first() !== null) break;
                if(is_null($currentFolder->parentfolder_id)) break;
            }
        }

        return response()->json($folderPaths);

    }

}
