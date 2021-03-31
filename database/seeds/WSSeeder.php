<?php

use App\activitylog;
use App\numbersT;
use Illuminate\Database\Seeder;
use App\rating;
use App\UserModel;
use App\WorksheetModel;

class WSSeeder extends Seeder
{
    public function run()
    {
        factory(App\WorksheetModel::class, numbersT::ws())->create()->each(function ($ws) {
            $author = UserModel::where('id', $ws->author)->first();

            /**
             * Update ratings
             */
            rating::update($author->username);

            /**
             * Update activitylog of poster
             */
            activitylog::post_ws($author->username, $ws->id);
        });
    }
}
