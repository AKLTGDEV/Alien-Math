<?php

use App\activitylog;
use App\numbersT;
use App\rating;
use App\SAQ;
use App\UserModel;
use Illuminate\Database\Seeder;

class SAQSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\SAQ::class, numbersT::saq())->create()->each(function ($q) {
            $author = UserModel::where('username', $q->uploader)->first();

            /**
             * Store Body and Explanations in database
             * 
             */
            $faker = Faker\Factory::create();
            $text = $faker->unique()->paragraph(1);
            Storage::disk('local')->put("saq/body/$q->id", $text);
            Storage::disk('local')->put("saq/explanation/$q->id", $faker->unique()->paragraph(2));

            echo "Creating SAQ: " . md5($text) . "..\n";

            $q->digest = $text;
            $q->save();

            /**
             * Update ratings and activity log of author
             */
            rating::update($author->username);
            activitylog::post_saq($author->username, $q->id);
        });
    }
}
