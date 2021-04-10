<?php

use App\numbersT;
use App\SAQ;
use App\TagsModel;
use App\UserModel;
use Faker\Generator as Faker;

$factory->define(App\SAQ::class, function (Faker $faker) {

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
    $correct = $faker->unique()->paragraph(1);
    $type_list = [
        "P1", "P2", "P3", "P4", "P5", "P6",
        "S1", "S2", "S3", "S4",
    ];
    $type = $type_list[array_rand($type_list)];
    $difficulty = rand(1, 3);

    $rating = 0;
    switch ($difficulty) {
        case 1:
            $rating = 800;
            break;

        case 2:
            $rating = 1000;
            break;

        case 3:
            $rating = 1200;
            break;

        default:
            # code...
            break;
    }

    $author_id = rand(1, numbersT::users());
    $author = UserModel::where('id', $author_id)->first();

    return [
        'correct' => $correct,
        'type' => $type,
        'difficulty' => $difficulty,
        'rating' => $rating,
        'topics' => implode(",", $taglist),
        'uploader' => $author->username,
    ];
});
