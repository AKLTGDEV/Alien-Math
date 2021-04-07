<?php

namespace App\utils;

use App\numbersT;
use App\TagsModel;
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
        $explanation = $faker->unique()->paragraph(1);

        $opt1 = $faker->unique()->sentence(5);
        $opt2 = $faker->unique()->sentence(5);
        $opt3 = $faker->unique()->sentence(5);
        $opt4 = $faker->unique()->sentence(5);

        $tags_halfmark = floor(numbersT::tags() / 2);
        $tag1 = TagsModel::where('id', rand(1, $tags_halfmark))->first();
        $tag2 = TagsModel::where('id', rand($tags_halfmark + 1, numbersT::tags()))->first();
        $tag3 = TagsModel::where('id', rand(1, numbersT::tags()))->first();
        while ($tag3 == $tag1 || $tag3 == $tag2) {
            $tag3 = TagsModel::where('id', rand(1, numbersT::tags()))->first();
        }

        $correct = rand(1, 4);

        return [
            "type" => "MCQ",
            "body" => $body,
            "opts" => [
                $opt1, $opt2, $opt3, $opt4
            ],
            "correct" => $correct,
            "explanation" => $explanation,
            "topics" => [
                $tag1->id,
                $tag2->id,
                $tag3->id,
            ]
        ];
    }

    public static function saq()
    {
        /**
         * 
         * Return JSON of a random SAQ
         * 
         */


        $faker = Factory::create();
        $body = $faker->unique()->paragraph(1);
        $correct = $faker->unique()->paragraph(1);
        $explanation = $faker->unique()->paragraph(1);

        $tags_halfmark = floor(numbersT::tags() / 2);
        $tag1 = TagsModel::where('id', rand(1, $tags_halfmark))->first();
        $tag2 = TagsModel::where('id', rand($tags_halfmark + 1, numbersT::tags()))->first();
        $tag3 = TagsModel::where('id', rand(1, numbersT::tags()))->first();
        while ($tag3 == $tag1 || $tag3 == $tag2) {
            $tag3 = TagsModel::where('id', rand(1, numbersT::tags()))->first();
        }

        return [
            "type" => "SAQ",
            "body" => $body,
            "correct" => $correct,
            "explanation" => $explanation,
            "topics" => [
                $tag1->id,
                $tag2->id,
                $tag3->id,
            ]
        ];
    }

    public static function sqa()
    {
        /**
         * 
         * Return JSON of a random SQA
         * 
         */


        $faker = Factory::create();
        $body = $faker->unique()->paragraph(1);

        $opt1 = $faker->unique()->sentence(5);
        $opt2 = $faker->unique()->sentence(5);
        $opt3 = $faker->unique()->sentence(5);
        $opt4 = $faker->unique()->sentence(5);
        $explanation = $faker->unique()->paragraph(1);

        $tags_halfmark = floor(numbersT::tags() / 2);
        $tag1 = TagsModel::where('id', rand(1, $tags_halfmark))->first();
        $tag2 = TagsModel::where('id', rand($tags_halfmark + 1, numbersT::tags()))->first();
        $tag3 = TagsModel::where('id', rand(1, numbersT::tags()))->first();
        while ($tag3 == $tag1 || $tag3 == $tag2) {
            $tag3 = TagsModel::where('id', rand(1, numbersT::tags()))->first();
        }

        return [
            "type" => "SQA",
            "body" => $body,
            "opts" => [
                $opt1, $opt2, $opt3, $opt4
            ],
            "explanation" => $explanation,
            "topics" => [
                $tag1->id,
                $tag2->id,
                $tag3->id,
            ]
        ];
    }
}
