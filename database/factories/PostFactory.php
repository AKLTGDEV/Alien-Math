<?php

use Faker\Generator as Faker;
use App\TagsModel;
use App\UserModel;
use App\numbersT;

$factory->define(App\PostModel::class, function (Faker $faker) {
    $tags_halfmark = floor(numbersT::tags() / 2);
    $tag1 = TagsModel::where('id', rand(1, $tags_halfmark))->first();
    $tag2 = TagsModel::where('id', rand($tags_halfmark + 1, numbersT::tags()))->first();
    $tag3 = TagsModel::where('id', rand(1, numbersT::tags()))->first();
    while ($tag3 == $tag1 || $tag3 == $tag2) {
        $tag3 = TagsModel::where('id', rand(1, numbersT::tags()))->first();
    }
    $taglist = json_encode([$tag1->name, $tag2->name, $tag3->name]);
    $opts = "[\"A\",\"B\",\"C\",\"D\"]";

    $text_body = $faker->unique()->paragraph(3);
    $body_md = md5($text_body);

    $title = $faker->unique()->paragraph(1);

    Storage::put("posts/" . $body_md, $text_body);

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
    $author->nos_Q++;
    $author->save();

    $pretext = $title . " Question by " . $author->username;

    echo "Creating Post: " . $body_md . ".. [[ ${text_body} ]] \n";

    return [
        'type' => $type,
        'difficulty' => $difficulty,
        'rating' => $rating,
        'text' => $body_md,
        'opts' => $opts,
        'tags' => $taglist,
        'correctopt' => rand(1, 2),
        'author' => $author_id,
        'title' => $title,
        'slug' => str_slug($pretext),
    ];
});
