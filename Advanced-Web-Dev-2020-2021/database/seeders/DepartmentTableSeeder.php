<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Department::factory()->create(['departmentname' => 'Finance' ]);
        Department::factory()->create(['departmentname' => 'Controlling' ]);
        Department::factory()->create(['departmentname' => 'Development' ]);
        Department::factory()->create(['departmentname' => 'Marketing' ]);
        Department::factory()->create(['departmentname' => 'Human Resources' ]);
    }
}
