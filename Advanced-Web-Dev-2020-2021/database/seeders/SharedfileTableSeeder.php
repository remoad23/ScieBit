<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Sharedfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SharedfileTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Sharedfile::factory()->count(500)
            ->afterCreating(function($sharedfile,$faker)
            {
                $id = rand(0,1);

                if($sharedfile->user_owner_id != null)
                {
                    if($id)
                    {
                        $sharedfile->user_requester_id = DB::table('user')->inRandomOrder()->whereNotIn('id',[$sharedfile->user_owner_id])->value('id');
                        $sharedfile->save();
                    }
                    else{
                        $sharedfile->admin_requester_id = DB::table('admin')->value('id');
                        $sharedfile->save();
                    }

                }
                else
                {
                    if($id)
                    {
                        $sharedfile->user_requester_id = DB::table('user')->inRandomOrder()->value('id');
                        $sharedfile->save();
                    }
                    else
                    {
                        $sharedfile->admin_requester_id = DB::table('admin')->inRandomOrder()->whereNotIn('id',[$sharedfile->admin_owner_id])->value('id');
                        $sharedfile->save();
                    }

                }

            }
            )->create();
    }
}
