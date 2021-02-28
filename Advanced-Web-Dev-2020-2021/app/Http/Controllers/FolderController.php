<?php

namespace App\Http\Controllers;

use App\Models\{Admin,User,File,Folder};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FolderController extends Controller
{

    public function index($id,$token)
    {
        $user = User::where(['id' => $id, 'user-token' => $token])->first() ??
            Admin::where(['id' => $id, 'admin-token' => $token])->first();

        $folders = null;

        if($user instanceof User)
        {
            $folders = Folder::where([
                'user_owner_id' => $id,
                'parentfolder_id' => null,
                'department_id' => null])
                ->get();
        }
        else{
            $folders = Folder::where([
                'admin_owner_id' => $id,
                'parentfolder_id' => null,
                'department_id' => null])
                ->get();
        }

        return response()->json([$folders]);
    }

    public function store(Request $request,$id,$token)
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
            $newFile->filetype = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);;
            $newFile->file = $file->hashName();
            $newFile->filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
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

    public function show($id,$token,$folderid)
    {
        $folder = Folder::where(['parentfolder_id' => $folderid])->get();

        return response()->json([$folder]);
    }

    public function delete($id,$token,$folderid)
    {
        $folder = Folder::where(['id' => $folderid])->first();
        $folder->delete();
    }

    public function download($id,$token,$folderid)
    {
        //mainfolder
        $folder = Folder::where('id',$folderid)->get()->first();

        if(count(File::where(['parentfolder_id' => $folder->id])->get()) == 0){
            return redirect()->route('docs.redirect');
        }

        //folder that are inside the mainfolder
        $subfolder = Folder::where(['parentfolder_id' => $folder->id])->get();

        $zip = new \ZipArchive;

        if( $zip->open($folder->foldername.".zip",\ZipArchive::CREATE) ){

            // add main folder and their files
            if($folder->has('files')->get())
            {
                foreach($folder->files()->get() as $file)
                {
                    $zip->addFile(storage_path("app/files/".$file->file),$folder->folder."/".$file->filename);
                }
            }

            // iterate recursively through the "tree" structure (folder -> childfolder ->childofchildfolder.....)
            if(count($subfolder) > 0)
            {
                //add subfolder and their files
                foreach($subfolder as $sfolder)
                {
                    $this->iterateFolder($zip,$sfolder);
                }
            }
        }
        $zip->close();
        return response()
            ->download( $folder->foldername . ".zip", $folder->foldername . ".zip")
            ->deleteFileAfterSend(true);
    }

    /**
     * Used to iterate through folders ( tree structure) recursively
     * @param $zip the zip file the folders will be inside
     * @param $folder the folders to iterate
     */
    private function iterateFolder(&$zip,$folder)
    {
        //add files to currentFolder we are inside,if there are any files
        if($folder->has('files')->get())
        {
            foreach($folder->files()->get() as $folderfile)
            {
                $zip->addFile(storage_path("app/files/".$folderfile->file),$folder->folder."/".$folderfile->filename);
            }
        }

        // iterate through other folders which are inside the folder,if there are any
        if( $folder->has('folders')->get() )
        {
            foreach($folder->folders()->get() as $ToIterateFolder )
            {
                $this->iterateFolder($zip,$ToIterateFolder);
            }
        }
    }

    /**
     * Move file to another controller
     */
    public function moveTo(Request $request,$token,$id)
    {
        $user = User::where(['id' => $id, 'user-token' => $token])->first() ??
            Admin::where(['id' => $id, 'admin-token' => $token])->first();

        $folderID = intval(str_replace('"','',$request->folderID));
        $folderIdTomoveIt = intval(str_replace('"','',$request->folderIdToMoveIt));

        $folder = Folder::where('id',$folderID)->first();
        if($folderIdTomoveIt == 0){
            $folder->parentfolder_id = null;
        }
        else{
            $folder->parentfolder_id = $folderIdTomoveIt;
        }

        $folder->save();
    }

}
