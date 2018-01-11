<?php namespace Quangtrong\News\Updates;

use Carbon\Carbon;
use Quangtrong\News\Models\Posts;
use Quangtrong\News\Models\Categories;
use October\Rain\Database\Updates\Seeder;
use Faker;

class SeedAllTables extends Seeder
{

    public function run()
    {
        $faker = Faker\Factory::create();
        for ($i=0; $i < 20; $i++) { 
            $title = $faker->sentence($nbWords = 6, $variableNbWords = true);
            $create = $faker->unixTime($max = 'now');
            Posts::create([
                'title' => $title,
                'slug' => str_slug($title,'-'),
                'introductory' => $faker->paragraph($nbSentences = 3, $variableNbSentences = true),
                'content' => $faker->realText($maxNbChars = 500, $indexSize = 2),
                'published_at' =>   $faker->unixTime($max = 'now'),
                'created_at' => $create,
                'updated_at' => $create,
                'image'=> $faker->imageUrl($width = 640, $height = 480),
                'status'=> 1,
                'featured' => 2,
                'last_send_at' => $faker->date($format = 'Y-m-d', $max = 'now'),
                'category_id' => 0
            ]);
        }
        for ($i=0; $i < 10; $i++) { 
            $name = $faker->sentence($nbWords = 4, $variableNbWords = true);
            Categories::create([
                'name' => $name,
                'slug' => str_slug($name,'-'),
                'status' => 1,
                'hidden' => 2,
            ]);
        }
    }

}
