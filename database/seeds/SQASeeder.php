<?php

use App\activitylog;
use App\numbersT;
use App\rating;
use App\UserModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class SQASeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\SQA::class, numbersT::saq())->create()->each(function ($q) {
            $author = UserModel::where('username', $q->uploader)->first();

            /**
             * Store Body and Explanations in database
             * 
             */
            $faker = Faker\Factory::create();
            $text = $faker->unique()->paragraph(1);
            Storage::disk('local')->put("sqa/body/$q->id", $text);
            Storage::disk('local')->put("sqa/explanation/$q->id", $faker->unique()->paragraph(2));

            echo "Creating SQA: " . md5($text) . "..\n";

            $q->digest = $text;
            $q->save();

            /**
             * Update ratings and activity log of author
             */
            rating::update($author->username);
            activitylog::post_sqa($author->username, $q->id);
        });
    }
}
