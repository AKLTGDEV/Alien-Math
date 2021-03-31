<?php

use Faker\Generator as Faker;
use App\users;
use Illuminate\Support\Facades\Storage;

$factory->define(App\UserModel::class, function (Faker $faker) {
    $uname = $faker->unique()->userName;
    echo "Creating user: ".$uname."..\n";

    $bio = $faker->text(24);
    users::storebio($uname, $bio);


    // HAVE RANDOM IMAGE
    /*$image_data = file_get_contents('https://picsum.photos/100/100');
    Storage::put("profilepx/$uname" , $image_data);*/

    return [
        'name' => $faker->name,
        'username' => $uname,
        'email' => $faker->unique()->safeEmail,
        'password' => bcrypt("13141314"),
        'remember_token' => str_random(10),
        //'tags' => $taglist,
        //'bio' => "--",
    ];
});
