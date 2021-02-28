<?php

namespace Database\Seeders;

use App\Models\File;
use App\Models\Admin;
use Illuminate\Database\Seeder;


class FileTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        File::factory()->count(2500)->create();
    }
}
