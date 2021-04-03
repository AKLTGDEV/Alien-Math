<?php

namespace App\utils;

use Faker\Factory;

class randq
{
    public static function mcq()
    {
        /**
         * 
         * Return JSON of a random MCQ
         * 
         */


        $faker = Factory::create();
        $body = $faker->unique()->paragraph(1);

        $opt1 = $faker->unique()->sentence(5);
        $opt2 = $faker->unique()->sentence(5);
        $opt3 = $faker->unique()->sentence(5);
        $opt4 = $faker->unique()->sentence(5);

        $correct = rand(1, 4);

        return [
            "type" => "MCQ",
            "body" => $body,
            "opts" => [
                $opt1, $opt2, $opt3, $opt4
            ],
            "correct" => $correct
        ];
    }
}
