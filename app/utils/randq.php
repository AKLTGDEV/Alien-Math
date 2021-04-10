<?php

namespace App\utils;

use App\numbersT;
use App\PostModel;
use App\SAQ;
use App\SQA;
use App\TagsModel;
use Faker\Factory;

class randq
{

    // NEW METHODS BELOW

    public static function mcq()
    {
        // Pull a random MCQ from the database, return the data

        $q = PostModel::where("id", rand(1, PostModel::count() ))->first();

        $q_topics = [];
        foreach (json_decode($q->tags) as $tag) {
            $q_topics[] = TagsModel::where("name", $tag)
                ->first()
                ->id;
        }

        return [
            "type" => "MCQ",
            "id" => $q->id, // ID of the original question
            "body" => $q->getBody(),
            "opts" => json_decode($q->opts),
            "correct" => $q->correctopt,
            "explanation" => $q->getExplanation(),
            "topics" => $q_topics,
        ];
    }

    public static function saq()
    {
        $q = SAQ::where("id", rand(1, SAQ::count()) )->first();

        $q_topics = [];
        foreach (explode(",", $q->topics) as $tag) {
            $q_topics[] = TagsModel::where("name", $tag)
                ->first()
                ->id;
        }

        return [
            "type" => "SAQ",
            "id" => $q->id, // ID of the original question
            "body" => $q->getBody(),
            "correct" => $q->correct,
            "explanation" => $q->getExplanation(),
            "topics" => $q_topics,
        ];
    }

    public static function sqa()
    {
        $q = SQA::where("id", rand(1, SQA::count()) )->first();

        $q_topics = [];
        foreach (explode(",", $q->topics) as $tag) {
            $q_topics[] = TagsModel::where("name", $tag)
                ->first()
                ->id;
        }

        return [
            "type" => "SQA",
            "id" => $q->id, // ID of the original question
            "body" => $q->getBody(),
            "opts" => [
                $q->O1,
                $q->O2,
                $q->O3,
                $q->O4,
            ],
            "explanation" => $q->getExplanation(),
            "topics" => $q_topics,
        ];
    }



    // OLD METHODS BELOW

    public static function mcq_old()
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
            "id" => 0, // ID of the original question
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

    public static function saq_old()
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
            "id" => 0, // ID of the original question
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

    public static function sqa_old()
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
            "id" => 0, // ID of the original question
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
