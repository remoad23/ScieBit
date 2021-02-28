<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $picture = Storage::putFile('public', 'public/Images/MockProfilePictures/pexels-mentatdgt-1138903.jpg');
        $picture = basename($picture);
        DB::table('admin')->insert([
            'name' => 'admin',
            'lastname' => 'admin',
            'email' =>'admin@gmail.com',
            'password' => Hash::make('adminpw'),
            'picture' => $picture,
        ]);

    }
}
