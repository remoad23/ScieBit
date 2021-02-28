<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Department;
use App\Models\Departmentadmin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentadminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
   //     Departmentadmin::factory()->count(50,100)->create();

        for($x = 0; $x < 150; $x++)
        {
            Departmentadmin::firstOrCreate([
                'admin_id' => Admin::all()->random()->id,
                'department_id' => Department::all()->random()->id,
            ]);
        }

    }
}
