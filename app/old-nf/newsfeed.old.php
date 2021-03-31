<?php

namespace App;

use App\PostModel;
use App\posts;
use App\TagsModel;
use App\users;
use Illuminate\Support\Facades\Storage;

class newsfeed
{

    public static function newsfeed($user, $request)
    {
        /**
         * A proper attempt to generate a newsfeed.
         * 
         * STEP 1: Check if the user is logging in for the first time, by checking how many
         *         tags he has. If there are none, that surely implies that it's his 
         *         first time. In that case, return null. Otherwise, return newsfeed()
         * 
         * STEP 2: .. FIXME ..
         */

        if (users::gettags($user->username) == null) {
            return null;
        } else {

            /*$utags = json_decode(users::gettags($user->username)); //All the user's tags.

            $user_post_list = PostModel::where('author', $user->id)->get(); //All the user's posts.
            $user_post_tags = array();
            foreach ($user_post_list as $user_post) {
                $tags = json_decode($user_post->tags);
                foreach ($tags as $user_post_tag) {
                    if(!in_array($user_post_tag, $user_post_tags)){
                        array_push($user_post_tags, $user_post_tag);
                    }
                }
            }

            $user_ws_list = WorksheetModel::where('author', $user->id)->get(); //All the user's Worksheets.
            $user_ws_tags = array();
            foreach ($user_ws_list as $user_ws) {
                $tags = json_decode($user_ws->tags);
                foreach ($tags as $user_ws_tag) {
                    if(!in_array($user_ws_tag, $user_ws_tags)){
                        array_push($user_ws_tags, $user_ws_tag);
                    }
                }
            }

            $tags_final = array();
            foreach ($utags as $utag) {
                array_push($tags_final, $utag);
            }
            foreach ($user_post_tags as $uptag) {
                array_push($tags_final, $uptag);
            }
            foreach ($user_ws_tags as $uwstag) {
                array_push($tags_final, $uwstag);
            }

            $tags_final = array_unique($tags_final);

            $posts_stage_1 = array();

            foreach ($tags_final as $final_tag) {
                $tag_posts = PostModel::orderBy('attempts', 'DESC')->where('tags', 'like', "%".$final_tag."%")->get();
                if(count($tag_posts) == 0){
                    continue;
                } else if(count($tag_posts) < 3){
                    continue;
                } else {
                    // Get the top 3 posts from the current tag.
                    array_push($posts_stage_1, $tag_posts[0]);
                    array_push($posts_stage_1, $tag_posts[1]);
                    array_push($posts_stage_1, $tag_posts[2]);
                }
            }

            dd(array_unique($posts_stage_1));*/


            return newsfeed::newsfeed2($user, $request);
        }
    }

    public static function newsfeed2($user, $request)
    {
        $nf_posts = $request->session()->get('newsfeed');
        if ($nf_posts == null || $nf_posts == "[]") {

            /**
             * FIXMEE
             * 
             * DOCUMENTATION
             */

            $populartags = array();
            $utags = json_decode(users::gettags($user->username));
            $utags_each_net = array();
            foreach ($utags as $tag) {
                $tag = TagsModel::where('name', $tag)->first();
                $utags_each_net[$tag->name] = $tag->net;
            }
            asort($utags_each_net);
            $utags_each_net_KEYS = array_keys($utags_each_net);
            array_push($populartags, $utags_each_net_KEYS[count($utags_each_net_KEYS) - 1]);
            array_push($populartags, $utags_each_net_KEYS[count($utags_each_net_KEYS) - 2]);

            /**
             * NOTE $populartags now contains the 2 tags from user's tag list which have the
             * maximum number of questions posted.
             */

            /**
             * BEWARE below, $ans_tags_list might turn out to be empty (If the user is new).
             * If it is so, keep $populartags as is.
             */

            $ans_tags_list = array();
            //$user_ans = json_decode($user->answers);
            $user_ans = json_decode(Storage::get('answers/' . $user->username), true);
            foreach ($user_ans as $Q => $ans) {
                $Q = substr($Q, 1);
                $post = PostModel::where('id', $Q)->first();
                $p_tags = json_decode($post->tags);
                foreach ($p_tags as $t) {
                    array_push($ans_tags_list, $t);
                }
            }
            sort($ans_tags_list);
            $ans_tags_list_FINAL = array();
            foreach ($ans_tags_list as $tag_index => $tag) {
                if (in_array($tag, array_keys($ans_tags_list_FINAL)) == false) {
                    $ans_tags_list_FINAL[$tag] = 1;
                } else {
                    $ans_tags_list_FINAL[$tag]++;
                }
            }
            asort($ans_tags_list_FINAL);
            $ans_tags_list_FINAL = array_keys($ans_tags_list_FINAL);
            $ans_tags_list_FINAL = array_reverse($ans_tags_list_FINAL);

            if (count($ans_tags_list_FINAL) != 0) {
                if (count($ans_tags_list_FINAL) < 3) {
                    foreach ($ans_tags_list_FINAL as $___atl) {
                        array_push($populartags, $___atl);
                    }
                } else {
                    array_push($populartags, $ans_tags_list_FINAL[0]);
                    array_push($populartags, $ans_tags_list_FINAL[1]);
                    array_push($populartags, $ans_tags_list_FINAL[2]);
                }
            }

            $__TAGSET__ = $populartags;

            /**
             * $__TAGSET__ contains the tags we have to pick posts from.
             */
            $posts = array();
            foreach ($__TAGSET__ as $tag) {
                $P = PostModel::where('tags', 'like', '%' . $tag . '%')->get();
                foreach ($P as $p) {
                    array_push($posts, $p->id);
                }
            }
            $posts = array_unique($posts);
            //Get the top 20 most attempted posts from this list.
            $post_with_attempts = array();
            foreach ($posts as $post_id) {
                $post_current = posts::get($post_id);
                $attempts = $post_current['right'] + $post_current['wrong'];
                $post_with_attempts[$post_id] = $attempts;
            }
            asort($post_with_attempts);
            $post_with_attempts = array_keys($post_with_attempts);
            $post_with_attempts = array_reverse($post_with_attempts);


            $FINAL_POSTS_LIST = array();
            if (count($post_with_attempts) <= 20) {
                $FINAL_POSTS_LIST = $post_with_attempts;
            } else {
                for ($i = 1; $i <= 20; $i++) {
                    array_push($FINAL_POSTS_LIST, $post_with_attempts[$i - 1]);
                }
            }
        } else {
            /**
             * keep a small random number of posts same from the last feed, 
             * and generate new ones.
             */
        }


        /**
         * $FINAL_POSTS_LIST conatins the list of posts that is to be returned
         * as $__NEWSFEED__.
         */

        $__NEWSFEED__ = array();
        foreach ($FINAL_POSTS_LIST as $t) {
            $post__ = posts::get($t);

            array_push($__NEWSFEED__, $post__);
        }

        shuffle($__NEWSFEED__);
        return $__NEWSFEED__;
    }
}
