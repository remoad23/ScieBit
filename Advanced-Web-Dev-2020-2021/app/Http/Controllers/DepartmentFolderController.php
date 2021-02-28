<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Departmentadmin;
use App\Models\Departmentuser;
use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Http\Request;

class DepartmentFolderController extends Controller
{
    public function index($id,$token,$departmentID)
    {
        $user = User::where(['id' => $id , 'user-token' => $token])->first() ??
            Admin::where(['id' => $id , 'admin-token' => $token])->first();

        $departmentUser = null;

        if($user instanceof User)
        {
            $departmentUser = Departmentuser::where(['user_id' => $user->id,'department_id' => $departmentID])->get()->first();
        }
        else{
            $departmentUser = Departmentadmin::where(['admin_id' => $user->id,'department_id' => $departmentID])->get()->first();
        }

        $departmentfolders = null;

        $departmentfolders = Folder::where([
            'department_id' => $departmentUser->department_id,
            'parentfolder_id' => null,
            ])->get();

        if( count($departmentfolders)  > 0 )
        {
            return response()->json([$departmentfolders]);
        }
        else{
            abort(404);
        }


    }

    public function store(Request $request,$departmentid,$id,$token)
    {
        $user = User::where(['id' => $id, 'user-token' => $token])->first() ??
            Admin::where(['id' => $id, 'admin-token' => $token])->first();

        $folderpath = [];
        $folderParentPath = [];

        // files from input element
        foreach($request->get('folderPath') as $folder)
        {
            $folderpath[] = json_decode($folder,true);
        }

        // paths and names of the folders
        foreach($request->get('folderParentPath') as $folder)
        {
            $folderParentPath[] = json_decode($folder,true);
        }


        $alreadyUsedParentPaths = [];
        $mainFolder = null;

        // determine by folderpaths which folder should be created
        // get rid of paths which have already been used for a folder so no folder will be created twice
        for($i = 0; $i < sizeof($folderParentPath); $i++)
        {
            if( array_key_exists($folderParentPath[$i][1],$alreadyUsedParentPaths) )
            {
                continue;
            }
            $newFolder = new Folder();
            $newFolder->foldername = $folderParentPath[$i][0];

            if(isset($request->parentFolderId) && $i === 0)
            {
                $newFolder->parentfolder_id = intval($request->parentFolderId);
                // get the first folder ( main parent folder to pass him as response)
                $mainFolder = $newFolder;
            }
            else if($i === 0)
            {
                $mainFolder = $newFolder;
            }

            if($user instanceof User)
            {
                $newFolder->user_owner_id = $user->id;
            }
            else{
                $newFolder->admin_owner_id = $user->id;
            }

            if($departmentid > 0 && $departmentid < 7)
            {
                $newFolder->department_id = $departmentid;
            }
            else{
                abort(404);
            }

            //if path has a "/" (a parentFolder)
            if(str_contains($folderParentPath[$i][1],"/") )
            {

                // get parent Route
                $parentSplitIndex = strrpos($folderParentPath[$i][1],"/");
                $parentPath = substr($folderParentPath[$i][1],0,$parentSplitIndex);

                // if a folder path has already been used get the id of it
                if( array_key_exists($parentPath,$alreadyUsedParentPaths) )
                {
                    $newFolder->parentfolder_id = $alreadyUsedParentPaths[$parentPath];
                    $alreadyUsedParentPaths[$folderParentPath[$i][1]] = $newFolder->getNextId();
                }
                // otherwise get id of new folder
                else{

                    foreach($folderParentPath as $folder)
                    {
                        if($folder[1] === $parentPath)
                        {
                            $newFolder->parentfolder_id = $folder[2]->id;
                            $alreadyUsedParentPaths[$folderParentPath[$i][1]] = $folder[2]->id;
                            break;
                        }
                    }
                }
            }
            // no parentfolder
            else{
                $alreadyUsedParentPaths[$folderParentPath[$i][1]] = $newFolder->getNextId();
            }

            // save Model of Folder inside third array index to access it later (parentfolder)
            $folderParentPath[$i][2] = $newFolder;
            $newFolder->folder = $folderParentPath[$i][1];
            $newFolder->save();
        }



        // assign each folder to the folders created above
        // assigning will be determined by the path the files have ( compare them with folder path)
        $i = 0;
        foreach($request->file('folderUpload') as $file)
        {
            $newFile = new File();
            $newFile->filetype = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
            $newFile->file = $file->hashName();
            $newFile->filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $newFile->department_id = $departmentid;
            $newFile->is_current_version = 1;

            if($user instanceof User)
            {
                $newFile->user_owner_id = $user->id;
            }
            else{
                $newFile->admin_owner_id = $user->id;
            }

            $newFile->parentfolder_id = $alreadyUsedParentPaths[$folderParentPath[$i][1]];

            $newFile->save();
            $newFile->version_group_id = $newFile->id;
            $newFile->save();
            $file->store('files');
            $i++;
        }


        return response()->json([$mainFolder]);
    }
}
