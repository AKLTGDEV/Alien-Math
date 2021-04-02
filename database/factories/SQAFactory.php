<?php

use App\numbersT;
use App\SQA;
use App\TagsModel;
use App\UserModel;
use Faker\Generator as Faker;

$factory->define(SQA::class, function (Faker $faker) {
    $tags_halfmark = floor(numbersT::tags() / 2);
    $tag1 = TagsModel::where('id', rand(1, $tags_halfmark))->first();
    $tag2 = TagsModel::where('id', rand($tags_halfmark + 1, numbersT::tags()))->first();
    $tag3 = TagsModel::where('id', rand(1, numbersT::tags()))->first();


    while ($tag3 == $tag1 || $tag3 == $tag2) {
        $tag3 = TagsModel::where('id', rand(1, numbersT::tags()))->first();
    }
    $taglist = [
        $tag1->name,
        $tag2->name,
        $tag3->name
    ];

    //Create the Question
    $type_list = [
        "P1", "P2", "P3", "P4", "P5", "P6",
        "S1", "S2", "S3", "S4",
    ];
    $type = $type_list[array_rand($type_list)];
    $difficulty = rand(1, 3);

    $author_id = rand(1, numbersT::users());
    $author = UserModel::where('id', $author_id)->first();

    return [
        'O1' => $faker->unique()->sentence(5),
        'O2' => $faker->unique()->sentence(5),
        'O3' => $faker->unique()->sentence(5),
        'O4' => $faker->unique()->sentence(5),
        'type' => $type,
        'difficulty' => $difficulty,
        'topics' => implode(",", $taglist),
        'uploader' => $author->username,
    ];
});
