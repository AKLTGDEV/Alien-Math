<?php

use Faker\Generator as Faker;
use Illuminate\Support\Facades\Storage;

$factory->define(App\TagsModel::class, function (Faker $faker) {
    $tagname = $faker->unique()->word;
    echo "Creating tag: ".$tagname."..\n";
    return [
        'name' => $tagname,
    ];
});
