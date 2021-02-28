<?php

namespace App\Http\Controllers\Drive;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\File;
use App\Models\User;
use App\Models\Keyword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MyDocumentController extends Controller
{
    public function index($id,$token)
    {
        //get current user/admin
        $user = User::where(['id' => $id , 'user-token' => $token])->first() ??
            Admin::where(['id' => $id , 'admin-token' => $token])->first();

        //get files of current admin/user
        if($user instanceof User)
        {
            $files = File::where([
                'user_owner_id' => $user->id,
                'is_current_version' => 1,
                'parentfolder_id' => null,
                'department_id' => null])
                ->get();
        }
        else
        {
            $files = File::where([
                'admin_owner_id' => $user->id,
                'is_current_version' => 1,
                'parentfolder_id' => null,
                'department_id' => null])
                ->get();
        }
        if($files != null){
            for($i = 0; $i < sizeOf($files); $i++){
                $files[$i]->keywords = Keyword::where('file_id',$files[$i]->id)->pluck('keyword');
                $files[$i]->version_count = File::where('version_group_id',$files[$i]->version_group_id)->count()-1;
            }
        }


        return response()->json([$files]);


    }

    public function show(Request $request,$hashname)
    {
        $file = File::where(['file' => $hashname,'user_owner_id' => session()->get('id')])
            ->orWhere(['file' => $hashname,'admin_owner_id' => session()->get('id')]);

        // Storage::get('files/02qeNZIj45bnvvmpkncDF6Hee6oMhbf5XRuR6bLl.jpg')
      //  Storage::get($hashname);
        back();
    }


    public function update()
    {
        return view('filetest');
    }

    public function store(Request $request,$id,$token)
    {

        $user = User::where(['id' => $id , 'user-token' => $token])->first() ??
            Admin::where(['id' => $id , 'admin-token' => $token])->first();

        $file = new File();
        $file->filetype = pathinfo($request->file('fileUpload')->getClientOriginalName(), PATHINFO_EXTENSION);
        $file->file = $request->file('fileUpload')->hashName();
        $file->filename = pathinfo($request->file('fileUpload')->getClientOriginalName(), PATHINFO_FILENAME);
        $file->is_current_version = 1;
        if(isset($request->parentFolderId))
        {
            $file->parentfolder_id = $request->parentFolderId;
        }

        //Fileupload
        $request->file('fileUpload')->store('files');

        if($user instanceof User)
        {
            $file->user_owner_id = $id;
        }
        else
        {
           $file->admin_owner_id = $id;
        }


        $file->save();
        $file->version_group_id = $file->id;

        $keywords = json_decode($request->keywords);
        foreach($keywords as $keywordName){
            $keyword = new Keyword();
            $keyword->keyword = $keywordName;
            $keyword->file_id = $file->id;
            $keyword->save();
        }
        $file->save();

        $file->version_count = 0;

        return response()->json([$file]);
    }

    /**
     * Download a requested filed
     */
    public function download($id,$token,$hashname)
    {
        $user = User::where(['id' => $id , 'user-token' => session()->get('_token')])->first() ??
            Admin::where(['id' => $id , 'admin-token' => session()->get('_token')])->first();

        $file = null;
        if($user instanceof User)
        {
            $file = File::where([
                'file' => $hashname,
                'is_current_version' => true])
                ->first();

            // make sure that the file belongs to user
            if(session()->get('id') != $id)
            {
                abort (404);
            }
        }else
        {
            $file = File::where([
                'file' => $hashname,
                'is_current_version' => true])->first();

            // make sure that the file belongs to user
            if(session()->get('id') != $id)
            {
                abort (404);
            }
        }



        $path_extension = pathinfo($file->file, PATHINFO_EXTENSION);
        return Storage::download("files/{$file->file}", $file->filename.'.'.$path_extension);
    }

    public function delete($id,$token,$fileid)
    {

        $file = File::find($fileid);

        Storage::delete("files/{$file->file}");
        $fileCopy = clone $file;

        $file->delete();

        return response()->json([$fileCopy]);
    }

    public function indexAll($id,$token)
    {
    //get current user/admin
            $user = User::where(['id' => $id , 'user-token' => $token])->first() ??
                Admin::where(['id' => $id , 'admin-token' => $token])->first();

            //get files of current admin/user
            if($user instanceof User)
            {
                $files = File::where([
                    'user_owner_id' => $user->id,
                    'is_current_version' => 1,
                    'department_id' => null])
                    ->get();
            }
            else
            {
                $files = File::where([
                    'admin_owner_id' => $user->id,
                    'is_current_version' => 1,
                    'department_id' => null])
                    ->get();
            }
            if($files != null){
                for($i = 0; $i < sizeOf($files); $i++){
                    $files[$i]->keywords = Keyword::where('file_id',$files[$i]->id)->pluck('keyword');
                    $files[$i]->version_count = File::where('version_group_id',$files[$i]->version_group_id)->count()-1;
                }
            }


            return response()->json([
                $files
            ]);
    }
}
