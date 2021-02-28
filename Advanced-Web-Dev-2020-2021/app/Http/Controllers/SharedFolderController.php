<?php

namespace App\Http\Controllers;

use App\Models\{Admin, File, Sharedfile, User, Departmentuser, Sharedfolder, Folder};
use Illuminate\Http\Request;

class SharedFolderController extends Controller
{

    public function index($id,$token)
    {
        $user = User::where(['id' => $id , 'user-token' => $token])->first() ??
            Admin::where(['id' => $id , 'admin-token' => $token])->first();

        $sharedFolder = null;
        $folders = [];

        // to make sure its not getting the files where user_requester_id or admin === null
        if($user->id === null) abort(404);

        // get files of current user/admin
        if($user instanceof User)
        {
            $sharedFolder = Sharedfolder::where('user_requester_id',$user->id)->get();
            foreach($sharedFolder as $shared)
            {
                $folders[] = $shared->folder()->first();
            }
        }
        else
        {
            $sharedFolder = Sharedfolder::where('admin_requester_id',$user->id)->get();
            foreach($sharedFolder as $shared)
            {
                $folders[] = $shared->folder()->first();
            }
        }

        return response()->json([$folders ?? "NotFound"]);

    }

    public function store(Request $request,$id,$token,$requester_id,$folder_id)
    {
        $user = User::where(['id' => $id , 'user-token' => $token])->first() ??
            Admin::where(['id' => $id , 'admin-token' => $token])->first();

        $sharedFile = new Sharedfolder();

        if($user instanceof User)
        {
            $sharedFile->folder_id = $folder_id;
            $sharedFile->user_owner_id = $user->id;
        }
        else
        {
            $sharedFile->folder_id = $folder_id;
            $sharedFile->admin_owner_id = $user->id;
        }

        //$requester = str_replace('"','',$request->get('requester'));
        if($request->userType == "User")
        {
            $sharedFile->user_requester_id = $requester_id;
        }
        else if($request->userType == "Admin"){
            $sharedFile->admin_requester_id = $requester_id;
        }


        $sharedFile->save();

        return response()->json([$sharedFile]);
    }

    public function download($id,$token,$folderid)
    {
        $sharedfolder = Sharedfolder::where('folder_id',$folderid)->first();
        //mainfolder
        $folder = $sharedfolder->folder()->first();
        if(count(File::where(['parentfolder_id' => $folder->id])->get()) == 0){
            return redirect()->route('docs.shared');

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

    public function delete(Request $request,$id,$token,$folderid)
    {

        $user = User::where(['id' => $id , 'user-token' => $token])->first() ??
            Admin::where(['id' => $id , 'admin-token' => $token])->first();


        $sharedFolder = null;

        if($user instanceof User) {
            $sharedFolder = Sharedfolder::where([
                'folder_id' => $folderid,
                'user_requester_id' => $id
            ]);
        }
        else {
            $sharedFolder = Sharedfolder::where([
                'folder_id' => $folderid,
                'admin_requester_id' => $id
            ]);
        }

        $sharedFolder->delete();
    }
}
