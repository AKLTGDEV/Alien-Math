<?php

use Faker\Generator as Faker;
use App\TagsModel;
use App\numbersT;
use App\UserModel;
use Carbon\Carbon;

$factory->define(App\WorksheetModel::class, function (Faker $faker) {

    $author_id = rand(1, numbersT::users());

    // Generate tags
    $tags_halfmark = floor(numbersT::tags() / 2);
    $tag1 = TagsModel::where('id', rand(1, $tags_halfmark))->first();
    $tag2 = TagsModel::where('id', rand($tags_halfmark + 1, numbersT::tags()))->first();
    $tag3 = TagsModel::where('id', rand(1, numbersT::tags()))->first();
    while ($tag3 == $tag1 || $tag3 == $tag2) {
        $tag3 = TagsModel::where('id', rand(1, numbersT::tags()))->first();
    }
    $taglist = json_encode([$tag1->name, $tag2->name, $tag3->name]);

    // Generate options
    $ws_opts = [];
    $nos = random_int(20,40);
    for ($i = 1; $i <= $nos; $i++) {
        array_push($ws_opts, ["A", "B", "C", "D"]);
    }

    // Generate random correct opts
    $correct_opts = [];
    for ($k = 1; $k <= $nos; $k++) {
        array_push($correct_opts, random_int(1, 4));
    }

    // Generate random time
    $time_mins = random_int(1, 30);

    // Generate a title
    $title = $faker->sentence(5, true);

    /**
     * Generate $nos different bodies
     */
    $bodies = [];
    for ($j = 1; $j <= $nos; $j++) {
        array_push($bodies, $faker->unique()->paragraph(2));
    }

    $pretext = $title . " Worksheet by " . UserModel::where("id", $author_id)->first()->username;

    $ws_name_ident = md5(rand(0, 69) . $author_id . $title . Carbon::now()->toDateTimeString() . rand(0, 69));
    $wsitem_contents = [
        "datetime" => Carbon::now()->toDateTimeString(),
        "title" => $title,
        "author" => $author_id, // FIXME
        "nos" => $nos,
        "bodies" => $bodies,
        "opts" => $ws_opts,
        "correct" => $correct_opts,
        "time" => $time_mins,
        "tags" => [$tag1->name, $tag2->name, $tag3->name],
    ];

    Storage::put("WS/$ws_name_ident", json_encode($wsitem_contents));


    echo "Creating Worksheet: $ws_name_ident..\n";

    return [
        'title' => $title,
        'slug' => str_slug($pretext),
        'nos' => $nos,
        'ws_name' => $ws_name_ident,
        'author' => $author_id,
        'tags' => $taglist,
        'invited' => "[]",
        'mins' => $time_mins
    ];
});
