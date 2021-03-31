<?php

namespace App\sidebar;

use App\PostModel;
use App\TagsModel;
use App\UserModel;
use App\users;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class tags
{
    public static function get($user)
    {
        $start = microtime(true);
        /**
         * Return a random list of tags that the user does not follow.
         * 
         * (To cut down compromise with speed)
         * (does not work, but always returns 3 tags)
         */

        $ret = [];

        $tnos = count(TagsModel::all()); // This is highly inefficient
        $utags = json_decode(users::gettags($user->username));
        for ($i = 1; count($ret) < 3; $i++) {
            $rand = rand(1, $tnos);

            $tag = TagsModel::where("id", $rand)
                //->exclude(['id', 'description', 'net', 'followers', 'created_at', 'updated_at'])
                ->first();
            if (!in_array($tag->name, $utags)) {
                array_push($ret, $tag->name);
            }
        }

        \Debugbar::info("TAGS " . round(microtime(true) - $start, 5));

        return [
            "flag" => true,
            "list" => $ret
        ];
    }

    public static function get_old($user)
    {
        $start = microtime(true);

        /**
         * 
         * TOPICS TO FOLLOW
         * ----------------------------
         * Make a list of the topics followed by the people user follows.
         * Return the top 3 by number of followers among following.
         */

        $following_tags_list = [];
        foreach (json_decode($user->following, true) as $person_id) {
            $following_person = UserModel::where("id", $person_id)->first();
            $following_person_tags = json_decode(users::gettags($following_person->username));

            foreach ($following_person_tags as $fpt) {
                array_push($following_tags_list, $fpt);
            }
        }

        // Weed out the topics already followed by the user
        $tags_final = [];
        $utags = json_decode(users::gettags($user->username));
        foreach ($following_tags_list as $t) {
            if (in_array($t, $utags)) {
                // This topic is already followed by user. skip.
            } else {
                array_push($tags_final, $t);
            }
        }

        $tags_final = array_count_values($tags_final);
        $tags = null;
        if (count($tags_final) >= 3) {
            arsort($tags_final);
            $tags_final = array_keys($tags_final);

            $tags = [
                $tags_final[0],
                $tags_final[1],
                $tags_final[2],
            ];

            $flag = true;
        } else {
            /**
             * 
             * In case the user is a newly
             * registered one, grab the user's answers 
             * and see the tags that appear mopst often.
             * 
             */

            //Grab all the questions answered by the user.
            $answered_list = json_decode(Storage::get('answers/' . $user->username), true);
            $ans_tags_list = [];
            foreach ($answered_list as $Q => $ans) {
                $Q = substr($Q, 1);
                $post = PostModel::where('id', $Q)->first();
                $p_tags = json_decode($post->tags);
                foreach ($p_tags as $t) {
                    array_push($ans_tags_list, $t);
                }
            }

            // Weed out the topics already followed by the user
            $ans_tags_list = array_count_values($ans_tags_list);
            $tags_final = [];
            $utags = json_decode(users::gettags($user->username));
            foreach (array_keys($ans_tags_list) as $t) {
                if (in_array($t, $utags)) {
                    // This topic is already followed by user. skip.
                } else {
                    array_push($tags_final, $t);
                }
            }

            if (count($tags_final) < 3) {
                $flag = false;
            } else {
                $tags = [
                    $tags_final[0],
                    $tags_final[1],
                    $tags_final[2],
                ];

                $flag = true;
            }
        }

        \Debugbar::info("TAGS " . round(microtime(true) - $start, 5));

        return [
            'flag' => $flag,
            'list' => $tags,
        ];
    }
}
