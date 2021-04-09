<?php

use Faker\Generator as Faker;
use App\users;
use Illuminate\Support\Facades\Storage;

$factory->define(App\UserModel::class, function (Faker $faker) {
    $uname = $faker->unique()->userName;
    echo "Creating user: " . $uname . "..\n";

    $bio = $faker->text(24);
    users::storebio($uname, $bio);

    $grades = [
        "P1", "P2", "P3", "P4", "P5", "P6",
        "S1", "S2", "S3", "S4",
    ];


    // HAVE RANDOM IMAGE
    /*$image_data = file_get_contents('https://picsum.photos/100/100');
    Storage::put("profilepx/$uname" , $image_data);*/

    return [
        'name' => $faker->name,
        'username' => $uname,
        'email' => $faker->unique()->safeEmail,
        'password' => bcrypt("13141314"),
        'remember_token' => str_random(10),
        'grade' => $grades[rand(0, 9)],
        'level' => rand(1, 3),
    ];
});
