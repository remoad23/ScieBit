<?php

namespace App\Http\Controllers\Drive;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Department;
use App\Models\Departmentadmin;
use App\Models\Departmentdocument;
use App\Models\Departmentuser;
use App\Models\File;
use App\Models\Keyword;
use App\Models\User;
use Illuminate\Http\Request;

class DepartmentDocumentController extends Controller
{
    public function index($id,$token)
    {
        $user = User::where(['id' => $id , 'user-token' => $token])->first() ??
            Admin::where(['id' => $id , 'admin-token' => $token])->first();


        $departmentuser = null;
        $documents = [];

        //get files of current admin/user
        if($user instanceof User)
        {
            $departmentuser = Departmentuser::where(['user_id' => $id])->get();
        }
        else
        {
            $departmentuser = Departmentadmin::where(['admin_id' => $id])->get();
        }

        // documents array index references the department_id to filter the documents according to their department_id
        foreach($departmentuser as $user)
        {
            $documents[$user->department_id] = File::where([
                'department_id' => $user->department_id,
                'parentfolder_id' => null,
                ])->get();
        }

        return response()->json([
            $documents[0] ?? null,
            $documents[1] ?? null,
            $documents[2] ?? null,
            $documents[3] ?? null,
            $documents[4] ?? null,
        ]);
    }

    public function show($department,$id,$token)
    {
        $user = User::where(['id' => $id , 'user-token' => $token])->first() ??
            Admin::where(['id' => $id , 'admin-token' => $token])->first();

        $departmentuser = null;
        $documents = null;

        $department = $department === "humanresources" ? "Human Resources" : $department;

        $department = Department::where(["departmentname" => "$department"])->first();

        if($department == null) return response()->json([null]);

        //get files of current admin/user
        if($user instanceof User)
        {
            $departmentuser = Departmentuser::where([
                'user_id' => $id,
                'department_id' => $department->id])
                ->first();

            if($departmentuser == null) return response()->json([null]);

            $documents = File::where([
                'department_id' => $departmentuser->department_id,
                'parentfolder_id' => null])->get();

            if($documents == null) return response()->json([null]);
        }
        else
        {
            $departmentuser = Departmentadmin::where([
                'admin_id' => $id,
                'department_id' => $department->id])
                ->first();

            if($departmentuser == null) return response()->json([null]);

            $documents = File::where([
                'department_id' => $departmentuser->department_id,
                'parentfolder_id' => null])->get();


            if($documents == null) return response()->json([null]);
        }
        if($documents != null){
            for($i = 0; $i < sizeOf($documents); $i++){
                $documents[$i]->keywords = Keyword::where('file_id',$documents[$i]->id)->pluck('keyword');
            }
        }

        return response()->json([[...$documents]]);
    }

    public function update()
    {

    }

    public function store(Request $request,$departmentid,$id,$token)
    {
        $user = User::where(['id' => $id , 'user-token' => $token])->first() ??
            Admin::where(['id' => $id , 'admin-token' => $token])->first();

        $file = new File();
        $file->filetype = pathinfo($request->file('fileUpload')->getClientOriginalName(), PATHINFO_EXTENSION);
        $file->file = $request->file('fileUpload')->hashName();
        $file->filename = pathinfo($request->file('fileUpload')->getClientOriginalName(), PATHINFO_FILENAME);
        $file->is_current_version = 1;

        if($departmentid > 0 && $departmentid < 7)
        {
            $file->department_id = $departmentid;
        }
        else{
            abort(404);
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

        $keywordsString = $request->input('keywords');
        $keywords = explode(',', $keywordsString);
        foreach($keywords as $keywordName){
            $keyword = new Keyword();
            $keyword->keyword = $keywordName;
            $keyword->file_id = $file->id;
            $keyword->save();
        }
        $file->save();

        return response()->json([$file]);
    }

    public function indexAll($department,$id,$token)
    {
        $user = User::where(['id' => $id , 'user-token' => $token])->first() ??
            Admin::where(['id' => $id , 'admin-token' => $token])->first();


        $departmentuser = null;
        $documents = null;

        $department = $department === "humanresources" ? "Human Resources" : $department;

        $department = Department::where(["departmentname" => "$department"])->first();

        if($department == null) return response()->json([null]);

        //get files of current admin/user
        if($user instanceof User)
        {
            $departmentuser = Departmentuser::where([
                'user_id' => $id,
                'department_id' => $department->id])
                ->first();

            if($departmentuser == null) return response()->json([null]);

            $documents = File::where([
                'department_id' => $departmentuser->department_id])
                ->get();

            if($documents == null) return response()->json([null]);
        }
        else
        {
            $departmentuser = Departmentadmin::where([
                'admin_id' => $id,
                'department_id' => $department->id])
                ->first();

            if($departmentuser == null) return response()->json([null]);

            $documents = File::where([
                'department_id' => $departmentuser->department_id])
                ->get();


            if($documents == null) return response()->json([null]);
        }
        if($documents != null){
            for($i = 0; $i < sizeOf($documents); $i++){
                $documents[$i]->keywords = Keyword::where('file_id',$documents[$i]->id)->pluck('keyword');
            }
        }

        return response()->json([[...$documents]]);
    }

    public function delete($id)
    {

    }

    public function getAuthorizedUserDepartments($id,$token)
    {
        $user = User::where(['id' => $id , 'user-token' => $token])->first() ??
            Admin::where(['id' => $id , 'admin-token' => $token])->first();


        $department_id = [];

        //get files of current admin/user
        if($user instanceof User)
        {
            $department = Departmentuser::where(['user_id' => $id])->get('department_id');
        }
        else
        {
            $department = Departmentadmin::where(['admin_id' => $id])->get('department_id');
        }

        foreach($department as $departmentVal)
        {
            $department_id[]  += $departmentVal->department_id;
        }

        $department_id = array_unique($department_id);

        // passed with spread operator to make sure index of array goes 0 to 4
        return response()->json([...$department_id]);
    }

}
