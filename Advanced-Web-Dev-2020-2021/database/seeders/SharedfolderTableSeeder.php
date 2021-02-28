<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Sharedfolder;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SharedfolderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SharedFolder::factory()->count(200)
                    ->afterCreating(function($sharedfolder,$faker)
                    {
                        $id = rand(0,1);

                        if($sharedfolder->user_owner_id != null)
                        {
                            if($id)
                            {
                                $sharedfolder->user_requester_id = DB::table('user')->inRandomOrder()->whereNotIn('id',[$sharedfolder->user_owner_id])->value('id');
                                $sharedfolder->save();
                            }
                            else{
                                $sharedfolder->admin_requester_id = DB::table('admin')->value('id');
                                $sharedfolder->save();
                            }

                        }
                        else
                        {
                            if($id)
                            {
                                $sharedfolder->user_requester_id = DB::table('user')->inRandomOrder()->value('id');
                                $sharedfolder->save();
                            }
                            else
                            {
                                $sharedfolder->admin_requester_id = DB::table('admin')->inRandomOrder()->whereNotIn('id',[$sharedfolder->admin_owner_id])->value('id');
                                $sharedfolder->save();
                            }

                        }

                    }
                    )->create();
    }
}
