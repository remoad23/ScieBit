<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\File;
use App\Models\Folder;
use App\Models\Sharedfile;
use App\Models\Keyword;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;
use function PHPUnit\Framework\isEmpty;

class FileController extends Controller
{
    public function show($id,$token,$folderid)
    {
        $files = File::where(['parentfolder_id' => $folderid])->get();
        if($files != null){
            for($i = 0; $i < sizeOf($files); $i++){
                $files[$i]->keywords = Keyword::where('file_id',$files[$i]->id)->pluck('keyword');
                $files[$i]->version_count = File::where('version_group_id',$files[$i]->version_group_id)->count()-1;
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

    /**
     * Set a file to another version of itself
     * @param Request $request
     */
    public function update(Request $request)
    {
        // the current Fileversion which is displayed
        $currentFile = File::where([
            'version_group_id' => $request->versionId,
            'is_current_version' => 1])
            ->get();
        // the Fileversion which is supposed to be the new currentFile to be displayed
        $selectedFile = File::where([
            'id' =>$request->selectedFileId,
            'version_group_id' => $request->versionId])
            ->get();

        // if both files can be found in the database
        if(!isEmpty($currentFile) && !isEmpty($selectedFile))
        {
            // switch all sharedFile file_id's to the selectedFile ID to
            // show users who share the this file the selectedFile
            $sharedFiles = $currentFile->sharedfile()->get();
            foreach($sharedFiles as $sharedFile)
            {
                $sharedFile->file_id = $selectedFile->id;
            }

            // set selected to the current version
            $currentFile->is_current_version = 0;
            $selectedFile->is_current_version = 1;
            return response()->json([$selectedFile]);
        }else{
            abort(404);
        }
    }

    public function updateFile(Request $request,$id,$token)
    {
        $user = User::where(['id' => $id , 'user-token' => $token])->first() ??
            Admin::where(['id' => $id , 'admin-token' => $token])->first();

        $updated_filename = $request->updatedFilename;
        $deleted_keywords = json_decode($request->deletedKeywords);
        $added_keywords = json_decode($request->addedKeywords);
        $file_id = $request->fileID;
        foreach($deleted_keywords as $keyword){
            Keyword::where([['file_id',$file_id],['keyword',$keyword]])->delete();
        }
        foreach($added_keywords as $keyword){
            $newKeyword = new Keyword();
            $newKeyword->file_id = $file_id;
            $newKeyword->keyword = $keyword;
            $newKeyword->save();
        }

        $file = File::find($file_id);
        if($updated_filename != 'null'){
            File::where('version_group_id',$file->version_group_id)->update(['filename' => $updated_filename]);
        }

         return response()->json($file);
    }

    public function addNewVersion(Request $request,$id,$token)
    {
        $user = User::where(['id' => $id , 'user-token' => $token])->first() ??
            Admin::where(['id' => $id , 'admin-token' => $token])->first();

        $versionId = str_replace('"','',$request->versionId);

        // the current Fileversion which is displayed
        $currentFile = File::where([
            'version_group_id' => $versionId,
            'is_current_version' => 1])
            ->first();


        // the Fileversion which is supposed to be the new currentFile to be displayed
        $selectedFile = new File();
        $selectedFile->version_group_id = $versionId;

        $selectedFile->filetype = pathinfo($request->file('fileUpload')->getClientOriginalName(), PATHINFO_EXTENSION);
        $selectedFile->file = $request->file('fileUpload')->hashName();
        $selectedFile->filename = pathinfo($request->file('fileUpload')->getClientOriginalName(), PATHINFO_FILENAME);
        $selectedFile->department_id = $currentFile->department_id;

        //Fileupload
        $request->file('fileUpload')->store('files');

        if($user instanceof User)
        {
            $selectedFile->user_owner_id = $id;
        }
        else
        {
            $selectedFile->admin_owner_id = $id;
        }
        // set selected to the current version
        $currentFile->is_current_version = 0;
        $selectedFile->is_current_version = 1;
        $selectedFile->save();
        $currentFile->save();

        // keywords that are already assigned to the current file
        $currentKeywords = Keyword::where('file_id', $currentFile->id)->get();

        foreach($currentKeywords as $currentKeyword){
            $newKeyword = new Keyword();
            $newKeyword->keyword = $currentKeyword->keyword;
            $newKeyword->file_id = $selectedFile->id;
            $newKeyword->save();
        }

        $addedKeywords = json_decode($request->keywords);
        foreach($addedKeywords as $addedKeyword){
            $newKeyword = new Keyword();
            $newKeyword->keyword = $addedKeyword;
            $newKeyword->file_id = $selectedFile->id;
            $newKeyword->save();

            if(count(Keyword::where('file_id',$selectedFile->id)->get()) > 5){
                $oldestKeyword = Keyword::where('file_id',$selectedFile->id)->oldest()->first();
                $oldestKeyword->delete();
            }

        }

        $selectedFile->keywords = Keyword::where('file_id',$selectedFile->id)->pluck('keyword');

        if(count(File::where(['version_group_id' => $versionId])->get() ) > 5)
        {
            $oldestVersion = File::where(['version_group_id' => $versionId])
                ->oldest()
                ->first();
            $oldestVersion->delete();
        }

        $selectedFile->version_count = File::where('version_group_id',$selectedFile->version_group_id)->count()-1;


        // switch all sharedFile file_id's to the selectedFile ID to
        // show users who share the this file the selectedFile
        $sharedFiles = Sharedfile::where(['file_id' => $currentFile->id])->get();
        if($sharedFiles->count() > 0)
        {
            foreach($sharedFiles as $sharedFile)
            {
                $sharedFile->file_id = $selectedFile->id;
            }

        }

        return response()->json([$selectedFile]);
    }

    /** get all possible file versions
     * @param $id
     * @param $token
     * @param $version_group_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVersions($id,$token,$version_group_id)
    {
        $files = File::where([
            'version_group_id' => $version_group_id,
            'is_current_version' => 0,
        ])->get();

        return response()->json($files);
    }

    /**
    * returns all files in this folder and every child folder
    */
    public function showAll($id,$token,$folderid)
    {
        $files = [];
        $this->getChildFiles($files,$folderid);

        if($files != null){
            for($i = 0; $i < sizeOf($files); $i++){
                $files[$i]->keywords = Keyword::where('file_id',$files[$i]->id)->pluck('keyword');
                $files[$i]->version_count = File::where('version_group_id',$files[$i]->version_group_id)->count()-1;
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

    /** Adds new FileVersion
     * @param Request $request
     * @param $id
     * @param $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function versionize(Request $request,$id,$token)
    {
        $fileID = str_replace('"','',$request->fileID);
        $versionId = str_replace('"','',$request->versionId);
        $user = User::where(['id' => $id , 'user-token' => $token])->first() ??
            Admin::where(['id' => $id , 'admin-token' => $token])->first();

        // the current Fileversion which is displayed
        $currentFile = File::where([
            'version_group_id' => $versionId,
            'is_current_version' => 1])
            ->first();

        // the fileversion which will be seen now for the user
        $selectedFile = File::where([
            'id' => $fileID,
            'version_group_id' => $versionId])
            ->first();

        // set selected to the current version
        $currentFile->is_current_version = 0;
        $selectedFile->is_current_version = 1;
        $selectedFile->save();
        $currentFile->save();

        $selectedFile->keywords = Keyword::where('file_id', $selectedFile->id)->pluck('keyword');
        $selectedFile->version_count = File::where('version_group_id',$selectedFile->version_group_id)->count()-1;


        // switch all sharedFile file_id's to the selectedFile ID to
        // show users who share the this file the selectedFile
        /*
        $sharedFiles = Sharedfile::where(['file_id' => $currentFile->id])->get();
        if($sharedFiles->count() > 0)
        {
            foreach($sharedFiles as $sharedFile)
            {
                  $sharedFile->file_id = $selectedFile->id;
            }

        } */

        return response()->json([$selectedFile]);
    }

    /**
     * Move file to another controller
     */
    public function moveTo(Request $request,$token,$id)
    {

        $fileID = intval(str_replace('"','',$request->fileID));
        $folderIdTomoveIt = intval(str_replace('"','',$request->folderIdToMoveIt));

        $file = File::where('id',$fileID)->first();
        if($folderIdTomoveIt == 0){
            $file->parentfolder_id = null;
        }
        else{
            $file->parentfolder_id = $folderIdTomoveIt;
        }

        $file->save();

        return response()->json([$folderIdTomoveIt,$fileID,$file]);
    }
}
