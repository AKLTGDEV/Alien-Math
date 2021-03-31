<?php

use App\activitylog;
use App\NotifsModel;
use Illuminate\Database\Seeder;
use App\numbersT;
use App\rating;
use App\tags;
use App\TagsModel;
use App\UserModel;

class PostSeeder extends Seeder
{
    public function run()
    {
        factory(App\PostModel::class, numbersT::posts())->create()->each(function ($p) {
            /**
             * For each post, update the tags table
             */
            $tags = json_decode($p->tags);
            $author = UserModel::where('id', $p->author)->first();

            foreach ($tags as $tagname) {
                $tag = TagsModel::where('name', $tagname)->first();
                $tag->net++;
                tags::newpostrecord($tag->name, $p->id);
    
                $tag->save();
            }

            /**
             * For each post, generate notification for followers.
             */
            if ($author->followers != "[]") {
                $followers_id_list = json_decode($author->followers);
                foreach ($followers_id_list as $fid) {
                    $newNotif = new NotifsModel;
                    $newNotif->for = $fid;
                    $newNotif->type = 1;
                    $newNotif->from = $author->id;
                    $newNotif->postid = $p->id;
                    $newNotif->seen = 0;

                    $newNotif->save();
                }
            }

            /**
             * Update ratings
             */
            rating::update($author->username);

            /**
             * Update activitylog of poster
             */
            activitylog::post_question($author->username, $p->id);
        });
    }
}
