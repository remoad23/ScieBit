<?php

namespace Database\Factories;

use App\Models\Sharedfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{Admin, File, User};
use Illuminate\Support\Facades\DB;

class SharedfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Sharedfile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $owner = $this->randomizeOwnerID();
        return [
            'file_id' => File::where('department_id',null)->get()->random()->id,
            'user_owner_id' => $owner[0],
            'admin_owner_id' => $owner[1],
            'user_requester_id' => null,
            'admin_requester_id' => null,
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
}
