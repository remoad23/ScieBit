<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\Folder;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{User,Admin};

class FileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = File::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $id = $this->randomizeOwnerID();
        $department_id = $this->randomizeDepartmentID();
        $insertInFolder = $this->insertFileIntoFolder($id);
        $parentfolder_id = $insertInFolder[0];
        if($parentfolder_id !== null){
            $department_id = $insertInFolder[1];
        }
        $file = $this->faker->file('public/Images/MockFiles','storage/app/files',false);
        $file_extension = pathinfo(storage_path().'/files/'.$file, PATHINFO_EXTENSION);

  //      $file = $this->faker->imageUrl($width = 640, $height = 480);
   //     Storage::put($file->, $contents);

        static $is_current_version = 1;

        return [
            'user_owner_id' => $id[0],
            'admin_owner_id' => $id[1],
            'file' => $file,
            'filename' => $this->faker->lexify('F?????'),
            'filetype' => $file_extension,
            'department_id' => $department_id,
            'parentfolder_id' => $parentfolder_id,
            'version_group_id' => $is_current_version++,
            'is_current_version' => true,
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
        $id = random_int(0,6);
        if($id === 0)
        {
            return random_int(1,5);
        }
        else
        {
            return null;
        }
    }

    private function insertFileIntoFolder($user_id)
    {
        if(rand(0,1) === 1){
            if($user_id[0] !== null){
                if(Folder::where('user_owner_id',$user_id[0])->count() > 0){
                    $parentfolder = Folder::where('user_owner_id',$user_id[0])->get()->random();
                    return [$parentfolder->id,$parentfolder->department_id];
                }
            }
            else{
                if(Folder::where('admin_owner_id',$user_id[1])->count() > 0){
                    $parentfolder = Folder::where('admin_owner_id',$user_id[1])->get()->random();
                    return [$parentfolder->id,$parentfolder->department_id];
                }
            }
        }
        return [null,null];

    }
}
