<?php

namespace Database\Factories;

use App\Models\Departmentadmin;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{Admin,Department};

class DepartmentadminFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Departmentadmin::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $admin_id = Admin::all()->random()->id;
        $department_id = Department::all()->random()->id;

        return [
            'admin_id' => $admin_id,
            'department_id' => $department_id,
        ];
    }
}
