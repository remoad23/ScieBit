<?php

namespace Database\Factories;

use App\Models\Departmentuser;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{User,Department};

class DepartmentuserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Departmentuser::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::all()->random()->id,
            'department_id' => Department::all()->random()->id,
        ];
    }
}
