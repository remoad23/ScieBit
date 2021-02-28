<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Departmentuser;
use App\Models\User;
use Illuminate\Database\Seeder;

class DepartmentuserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       // Departmentuser::factory()->count(rand(100,200))->create();
        for($x = 0; $x < 150; $x++)
        {
            Departmentuser::firstOrCreate([
                'user_id' => User::all()->random()->id,
                'department_id' => Department::all()->random()->id,
            ]);
        }
    }
}
