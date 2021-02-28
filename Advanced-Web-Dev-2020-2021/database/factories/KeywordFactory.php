<?php

namespace Database\Factories;

use App\Models\Keyword;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\File;

class KeywordFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Keyword::class;


    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $fileId = $this->randomizeFileID();
        $keywordArray = array('Finanzen','Sport','Freizeit','Projekt','Arbeit','Prototyp','Rezept','Essen','Notizen');

        return [
            'file_id' => $fileId,
            'keyword' => $this->faker->randomElement($keywordArray),
        ];
    }

    private function randomizeFileID()
    {
        return File::all()->random()->id;
    }

}
