<?php

namespace Database\Factories;

use App\Models\Sharedfolder;
use App\Models\Folder;
use Illuminate\Database\Eloquent\Factories\Factory;

class SharedfolderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Sharedfolder::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $folder = Folder::where('department_id',null)->get()->random();

        return [
            'folder_id' => $folder->id,
            'user_owner_id' => $folder->user_owner_id,
            'admin_owner_id' => $folder->admin_owner_id,
            'user_requester_id' => null,
            'admin_requester_id' => null,
        ];
    }
}
