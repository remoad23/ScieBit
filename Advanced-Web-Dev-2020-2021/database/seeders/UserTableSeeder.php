<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $picture = Storage::putFile('public', 'public/Images/MockProfilePictures/pexels-mentatdgt-937481.jpg');
        $picture = basename($picture);
        DB::table('user')->insert([
            'name' => 'user',
            'lastname' => 'user',
            'email' =>'user@gmail.com',
            'password' => Hash::make('userpw'),
            'picture' => $picture,
        ]);
    }
}
