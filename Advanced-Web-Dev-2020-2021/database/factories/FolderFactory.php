<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FolderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Folder::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $id = $this->randomizeOwnerID();
        $department_id = $this->randomizeDepartmentID();
        $name = $this->faker->lexify('Fol??');

        return [
            'user_owner_id' => $id[0],
            'admin_owner_id' => $id[1],
            'department_id' => $department_id,
            'foldername' => $name,
            'folder' => $name,
        ];
    }

    private function randomizeOwnerID()
    {
        $id = rand(0,1);
        if($id)
        {
            return [User::all()->random()->id,null];
        }
        else
        {
            return [null,Admin::all()->random()->id];
        }
    }

    private function randomizeDepartmentID()
    {
        $id = random_int(0,8);
        if($id === 0)
        {
            return random_int(1,5);
        }
        else
        {
            return null;
        }
    }

    public function insertRandomFolders(){
        $users = User::all();
        foreach($users as $user){
            $folders = Folder::where('user_owner_id',$user->id)->get();
            $folder_count = sizeOf($folders);
            for($i = 1; $i < $folder_count; $i++){
                if(rand(0,1) === 1){
                   $current_folder = $folders[$i];
                   $parentfolder = $folders[random_int(0,$i-1)];
                   $current_folder->parentfolder_id = $parentfolder->id;
                   $current_folder->folder = $parentfolder->folder .'/'. $current_folder->foldername;
                   $current_folder->department_id = $parentfolder->department_id;
                   $current_folder->save();
                }
            }
        }

        $admins = Admin::all();
        foreach($admins as $admin){
            $folders = Folder::where('admin_owner_id',$admin->id)->get();
            $folder_count = sizeOf($folders);
            for($i = 1; $i < $folder_count; $i++){
                if(rand(0,1) === 1){
                   $current_folder = $folders[$i];
                   $parentfolder = $folders[random_int(0,$i-1)];
                   $current_folder->parentfolder_id = $parentfolder->id;
                   $current_folder->folder = $parentfolder->folder .'/'. $current_folder->foldername;
                   $current_folder->department_id = $parentfolder->department_id;
                   $current_folder->save();
                }
            }
        }
    }
}
