<?php

namespace Database\Seeders;

use App\Models\Keyword;
use App\Models\File;
use Illuminate\Database\Seeder;


class KeywordTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Keyword::factory()->count(rand(2000,3000))->create();

        $keywordArray = array('Finanzen','Sport','Freizeit','Projekt','Arbeit','Prototyp','Rezept','Essen','Notizen');

        for($i = 0; $i < 3000; $i++){
            $file_id = File::all()->random()->id;
            $keyword_count = Keyword::where('file_id', $file_id)->count();
            if($keyword_count < 5){
                $idx = random_int(0,8);
                Keyword::firstOrCreate([
                    'file_id' => $file_id,
                    'keyword' => $keywordArray[$idx],
                ]);
            }
        }
    }
}
