<?php

namespace App;

use App\PostModel;
use App\UserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use App\activitylog;
use App\rating;

class posts
{
    public static function newsubmit($all, $author, $IMAGE_FLAG)
    {
        $post = new PostModel;

        //$options = [$all['option1'], $all['option2']];
        $options = [];
        for ($i = 1; $i <= $all['opt_nos']; $i++) {
            array_push($options, $all['option' . $i]);
        }

        $options = json_encode($options);

        $tags = explode(",", $all['question_tags']);
        $tags_new = array();
        foreach ($tags as $tag) {
            $tag = trim($tag);
            $tag_entry = TagsModel::where('name', $tag)->first();
            array_push($tags_new, $tag_entry->name);
        }
        $tags = $tags_new;

        /*if ($all['correct'] == '1') {
            $correct_opt = 1;
        } elseif ($all['correct'] == '2') {
            $correct_opt = 2;
        }*/
        $correct_opt = $all['correct'];

        //$author = UserModel::where('id', $all['authorid'])->first();
        //$author = Auth::user();

        $author->nos_Q++;
        $author->save();

        $body_md = md5($all['Qbody']);

        //$post->id = null;
        //if ($request->hasFile('img')) {
        if ($IMAGE_FLAG) {
            $image = $all['img'];
            $img_md = md5($all['img']);
            $image->storeAs("images/", $img_md, 'local');

            $post->image = $img_md;
        } else {
            $post->image = null;
        }
        $post->text = $body_md;
        $post->opts = $options;
        //$post->author = $all['authorid']; SECURITY ISSUE
        $post->author = $author->id;
        $post->correctopt = $correct_opt;
        $post->attempts = 0;
        $post->success = 0;
        $post->tags = json_encode($tags);

        /*if (array_key_exists("title", $all)) {
            $post->title = $all['title'];
        }*/
        $post->title = $all['title'];

        Storage::put("posts/" . $body_md, $all['Qbody']);

        $post->save(); //Hopefully.

        Storage::put("posts/explanation/" . $post->id, $all['explanation']);

        foreach ($tags as $tagname) {
            $tag = TagsModel::where('name', $tagname)->first();

            $tag->net++;
            /*$T_posts = json_decode($tag->posts);
            array_push($T_posts, $post->id);
            $tag->posts = json_encode($T_posts);*/

            tags::newpostrecord($tag->name, $post->id);

            $tag->save();
        }

        /**
         * Generate notification for followers.
         */

        if ($author->followers != "[]") {
            $followers_id_list = json_decode($author->followers);
            foreach ($followers_id_list as $fid) {
                $newNotif = new NotifsModel;
                $newNotif->for = $fid;
                $newNotif->type = 1;
                $newNotif->from = $author->id;
                $newNotif->postid = $post->id;
                $newNotif->seen = 0;

                $newNotif->save();
            }
        }

        activitylog::post_question($author->username, $post->id);
        return $post;
    }

    public static function get($id)
    {
        $post = PostModel::where('id', $id)->first();
        $author = UserModel::where('id', $post->author)->first();

        $user = Auth::user();

        if (posts::check_if_answered($user, $post)) {
            //$answered_list = json_decode($user->answers, true);
            $answered_list = json_decode(Storage::get('answers/' . $user->username), true);
            $given = $answered_list["q" . $post->id];
            $correctopt = $post->correctopt;
        } else {
            $given = null;
            $correctopt = null;
        }

        $app_url = Config::get('app.url');

        $post_body = posts::getbody($post->text);

        $img_present = false;
        if (strpos($post_body, '<img style=') !== false) {
            $img_present = true;
        }

        // Normalize the images
        $body_new = str_replace('<img style=', '<img id="postimg-' . $post->id . '" class="img-fluid" style=', $post_body);

        $own = false;
        if ($post->author == Auth::user()->id) {
            $own = true;
        }

        $post__ = array(
            'itemT' => 'post',
            'pid' => $post->id,
            'img_present' => $img_present,
            'name' => $author->name,
            'username' => $author->username,
            'profilepic' => "{$app_url}/user/{$author->username}/profilepic",
            'body' => $body_new,
            'opt_nos' => count(json_decode($post->opts, true)),
            'options' => json_decode($post->opts, true),
            'right' => $post->success,
            'wrong' => ($post->attempts - $post->success),
            'correctopt' => $correctopt,
            'givenopt' => $given,
            'tags' => $post->tags,
            'image' => $post->image,
            'title' => $post->title,
            'own' => $own,
        );

        return $post__;
    }

    public static function exists($id)
    {
        $post = PostModel::where('id', $id)->first();
        if ($post != null) {
            return true;
        } else {
            return false;
        }
    }

    public static function list($uname)
    {
        $current_user = UserModel::where("username", $uname)->first();
        $postlist_ret = array();
        $posts = PostModel::where("author", $current_user->id)->get();
        $user = Auth::user();
        foreach ($posts as $post) {
            if (posts::check_if_answered($user, $post)) {
                $answered_list = json_decode(Storage::get('answers/' . $user->username), true);
                $given = $answered_list["q" . $post->id];
            } else {
                $given = null;
            }
            $post__ = posts::get($post->id);

            array_push($postlist_ret, $post__);
        }

        return $postlist_ret;
    }

    public static function list_old($uname)
    {
        $postlist_ret = array();

        $posts = PostModel::all();
        foreach ($posts as $post) {
            //Check if the post is made by the author
            $author = UserModel::where('id', $post->author)->first();
            $post_author_uname = $author->username;
            if ($uname == $post_author_uname) {

                $user = Auth::user();

                if (posts::check_if_answered($user, $post)) {
                    //$answered_list = json_decode($user->answers, true);
                    $answered_list = json_decode(Storage::get('answers/' . $user->username), true);
                    $given = $answered_list["q" . $post->id];
                } else {
                    $given = null;
                }
                $post__ = posts::get($post->id);

                array_push($postlist_ret, $post__);
            }
        }

        return $postlist_ret;
    }

    public static function list_answered($user)
    {
        //$answered_list = json_decode($user->answers, true);
        $answered_list = json_decode(Storage::get('answers/' . $user->username), true);
        $ret = array();
        foreach ($answered_list as $q => $a) {
            $qno = explode("q", $q)[1];
            array_push($ret, $qno);
        }

        return $ret;
    }

    public static function check_if_answered($user, $post)
    {
        /**
         * Check if $user has already answered question $pid
         * or not. return likewise.
         *
         */

        $pid = $post->id;
        //$answered_list = json_decode($user->answers, true);
        $answered_list = json_decode(Storage::get('answers/' . $user->username), true);

        if (in_array("q" . $pid, array_keys($answered_list))) {
            return true;
        } else {
            return false;
        }
    }

    public static function check_rightwrong($user, $post)
    {
        /**
         * Check if the answer user submitted is right or wrong
         */

        $correct = $post->correctopt;
        $pid = $post->id;
        //$answered_list = json_decode($user->answers, true);
        $answered_list = json_decode(Storage::get('answers/' . $user->username), true);
        if ($answered_list["q" . $pid] == $correct) {
            return true;
        } else {
            return false;
        }
    }

    public static function check_rightwrong_pid($user, $pid)
    {
        /**
         * Check if the answer user submitted is right or wrong
         */
        $post = PostModel::where('id', $pid)->first();
        $correct = $post->correctopt;
        //$answered_list = json_decode($user->answers, true);
        $answered_list = json_decode(Storage::get('answers/' . $user->username), true);
        if ($answered_list["q" . $pid] == $correct) {
            return true;
        } else {
            return false;
        }
    }

    public static function getbody($digest)
    {
        return Storage::get('posts/' . $digest);
    }

    public static function answer($uname, $pid, $opt)
    {
        $User = UserModel::where("username", $uname)->first();
        $Post = PostModel::where('id', $pid)->first();
        $author = UserModel::where("id", $Post->author)->first();

        if (posts::check_if_answered($User, $Post)) {
            return "already answered";
        } else {

            /*$answered_list = json_decode($User->answers, true);
            $answered_list["q" . $pid] = $opt;
            $User->answers = json_encode($answered_list);*/
            $answered_list = json_decode(Storage::get('answers/' . $uname), true);
            $answered_list["q" . $pid] = $opt;
            Storage::put("answers/" . $uname, json_encode($answered_list));


            $User->nos_A++;

            /**
             * STEP 2: update posts.attempted
             */
            if ($opt == $Post->correctopt) {
                //Increase attempted, success
                $Post->attempts++;
                $Post->success++;


                $User->answers_t++;
                $User->save();
                $Post->save();

                rating::update($User->username);
                rating::update($author->username);

                activitylog::ans_question($User->username, $Post->id);

                return "SUCCESS";
            } else {
                //Increase attempted
                $Post->attempts++;
                $User->answers_f++;

                $User->save();
                $Post->save();

                rating::update($User->username);
                rating::update($author->username);

                activitylog::ans_question($User->username, $Post->id);

                return "FAILURE";
            }
        }
    }
}
