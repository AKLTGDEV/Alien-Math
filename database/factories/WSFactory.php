<?php

use Faker\Generator as Faker;
use App\TagsModel;
use App\numbersT;
use App\UserModel;
use App\utils\randq;
use Carbon\Carbon;

$factory->define(App\WorksheetModel::class, function (Faker $faker) {
    // NEW FORMAT

    $author_id = rand(1, numbersT::users());
    $time_mins = random_int(1, 30);
    $title = $faker->sentence(5, true);

    //No. of questions
    $nos = random_int(numbersT::ws_min_q(), numbersT::ws_max_q());

    $ws_name_ident = md5(rand(0, 69) . $author_id . $title . Carbon::now()->toDateTimeString() . rand(0, 69));

    $pretext = $title . " Worksheet by " . UserModel::where("id", $author_id)->first()->username;

    $wsitem_contents = [
        "datetime" => Carbon::now()->toDateTimeString(),
        "title" => $title,
        "author" => $author_id, // FIXME
        "nos" => $nos,
        "time" => $time_mins,
        /*"tags" => [
            $tag1->name,
            $tag2->name,
            $tag3->name
        ],*/
    ];

    $taglist = [];

    $contents = [];

    for ($i = 1; $i <= $nos; $i++) {
        $mcq = randq::mcq();
        $saq = randq::saq();
        $sqa = randq::sqa();

        foreach (array_merge(
            $mcq['topics'],
            $saq['topics'],
            $sqa['topics']
        ) as $topic) {
            $taglist[] = $taglist[] = TagsModel::where("id", $topic)
                ->first()
                ->name;
        }

        $contents[] = $mcq;
        $contents[] = $saq;
        $contents[] = $sqa;
    }

    $wsitem_contents['content'] = $contents;
    $wsitem_contents['tags'] = array_unique($taglist);

    $nos = count($contents);

    Storage::put("WS/$ws_name_ident", json_encode($wsitem_contents));

    echo "Creating Worksheet: $ws_name_ident..\n";

    return [
        'title' => $title,
        'slug' => str_slug($pretext),
        'nos' => $nos,
        'ws_name' => $ws_name_ident,
        'author' => $author_id,
        //'tags' => json_encode($taglist),
        'invited' => "[]",
        'mins' => $time_mins
    ];
});
