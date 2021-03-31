<?php

namespace App;

use App\PostModel;
use App\posts;
use App\TagsModel;
use App\users;
use DebugBar\DebugBar;
use Illuminate\Support\Facades\Storage;

class newsfeed
{
    public static function nfcontent($user, $request)
    {
        // Create a newsfeed and store the contents in $nfcontent session variable


        /**
         * 
         *  ** ALGORITHM **
         * 
         * 
         * STEP 1: Gather and rank all the Questions, WSs from each category:
         *      
         *      1. Posted material (1pt)
         *      2. Answered Material (2 pt)
         *      3. Posted materials of followings (6 pt)
         *      4. Answered materials of followings (7 pt)
         *      5. Top few posts, WS from tags being followed (5 pt)
         *      6. Latest few posts, WS from tags being followed (5 pt)
         */

        $pool = array();
        /**
         * [ 'id', 'type', 'pts' ]
         */
        $pool_post_idx = array();
        $pool_ws_idx = array();
        $followings = json_decode($user->following, true);
        $utags = json_decode(users::gettags($user->username));




        // POSTED MATERIAL
        $posted_questions = PostModel::where("author", $user->id)->get();
        foreach ($posted_questions as $__posted_question) {
            array_push($pool, [
                'id' => $__posted_question->id,
                'type' => "POST",
                'pts' => 1
            ]);

            array_push($pool_post_idx, $__posted_question->id);
        }
        $posted_ws = WorksheetModel::where("author", $user->id)->get();
        foreach ($posted_ws as $__posted_ws) {
            array_push($pool, [
                'id' => $__posted_ws->id,
                'type' => "WS",
                'pts' => 1
            ]);

            array_push($pool_ws_idx, $__posted_ws->id);
        }





        // ANSWERED MATERIAL
        $user_ans = json_decode(Storage::get('answers/' . $user->username), true);
        foreach ($user_ans as $Q => $ans) {
            $Q = substr($Q, 1);
            $ans_post = PostModel::where('id', $Q)->first();

            // Check if this one is already present in the pool. if 
            // yes, increment the points. Else, just normally push the item.
            if (in_array($ans_post->id, $pool_post_idx)) {
                // FIXME
                // or maybe.. IGNORE.
            } else {
                array_push($pool, [
                    'id' => $ans_post->id,
                    'type' => "POST",
                    'pts' => 2
                ]);
                array_push($pool_post_idx, $ans_post->id);
            }
        }
        $user_wsans = wsAttemptsModel::where("attemptee", $user->id)->get();
        foreach ($user_wsans as $__wsans) {
            if (in_array($__wsans->wsid, $pool_ws_idx)) {
                // Don't do shit.. IGNORE
            } else {
                array_push($pool, [
                    'id' => $__wsans->wsid,
                    'type' => "WS",
                    'pts' => 2
                ]);
                array_push($pool_ws_idx, $__wsans->wsid);
            }
        }




        // FOLLOWINGS POSTED
        foreach ($followings as $fid) {
            $following_posts = PostModel::where("author", $fid)->get();
            foreach ($following_posts as $f_post) {

                // Check if this one is already present in the pool. if 
                // yes, increment the points. Else, just normally push the item.
                if (in_array($f_post->id, $pool_post_idx)) {
                    for ($i = 0; $i < count($pool); $i++) {
                        $pool_item = $pool[$i];
                        if ($pool_item['type'] == "POST"  && $pool_item['id'] == $f_post->id) {
                            //dd($i);
                            $pool[$i]['pts'] += 6;
                        }
                    }
                } else {
                    array_push($pool, [
                        'id' => $f_post->id,
                        'type' => "POST",
                        'pts' => 6
                    ]);
                    array_push($pool_post_idx, $f_post->id);
                }
            }

            $following_ws = WorksheetModel::where("author", $fid)->get();
            foreach ($following_ws as $f_ws) {
                // Check if this one is already present in the pool. if 
                // yes, increment the points. Else, just normally push the item.
                if (in_array($f_ws->id, $pool_ws_idx)) {
                    for ($i = 0; $i < count($pool); $i++) {
                        $pool_item = $pool[$i];
                        if ($pool_item['type'] == "WS"  && $pool_item['id'] == $f_ws->id) {
                            //dd($i);
                            $pool[$i]['pts'] += 6;
                        }
                    }
                } else {
                    array_push($pool, [
                        'id' => $f_ws->id,
                        'type' => "WS",
                        'pts' => 6
                    ]);
                    array_push($pool_ws_idx, $f_ws->id);
                }
            }
        }






        // FOLLOWINGS ANSWERED
        foreach ($followings as $fid) {
            $f = UserModel::where("id", $fid)->first();

            $user_ans = json_decode(Storage::get('answers/' . $f->username), true);
            foreach ($user_ans as $Q => $ans) {
                $Q = substr($Q, 1);
                $ans_post = PostModel::where('id', $Q)->first();

                // Check if this one is already present in the pool. if 
                // yes, increment the points. Else, just normally push the item.
                if (in_array($ans_post->id, $pool_post_idx)) {
                    for ($i = 0; $i < count($pool); $i++) {
                        $pool_item = $pool[$i];
                        if ($pool_item['type'] == "POST"  && $pool_item['id'] == $ans_post->id) {
                            $pool[$i]['pts'] += 7;
                        }
                    }
                } else {
                    array_push($pool, [
                        'id' => $ans_post->id,
                        'type' => "POST",
                        'pts' => 7
                    ]);
                    array_push($pool_post_idx, $ans_post->id);
                }
            }

            $user_wsans = wsAttemptsModel::where("attemptee", $f->id)->get();
            foreach ($user_wsans as $__wsans) {
                if (in_array($__wsans->wsid, $pool_ws_idx)) {
                    for ($i = 0; $i < count($pool); $i++) {
                        $pool_item = $pool[$i];
                        if ($pool_item['type'] == "WS"  && $pool_item['id'] == $__wsans->wsid) {
                            $pool[$i]['pts'] += 7;
                        }
                    }
                } else {
                    array_push($pool, [
                        'id' => $__wsans->wsid,
                        'type' => "WS",
                        'pts' => 7
                    ]);
                    array_push($pool_ws_idx, $__wsans->wsid);
                }
            }
        }


        // Remove the bottom two-thirds of the items.
        usort($pool, function ($a, $b) {
            return $b['pts'] <=> $a['pts'];
        });
        $pool_new = array();
        for ($j = 0; $j < round((count($pool)) / 3); $j++) {
            array_push($pool_new, $pool[$j]);
        }
        $pool = $pool_new;




        // TOP MATERIAL
        foreach ($utags as $tag) {
            $posts = PostModel::where('tags', 'like', '%' . $tag . '%')->orderBy('attempts', 'inc')->get();
            //foreach ($posts as $top_post) {
            for ($i = 0; $i < 10; $i++) { // GET THE TOP 10
                $top_post = $posts[$i];

                $att_coefficient = round((1.5) * ($top_post->attempts));

                if (in_array($top_post->id, $pool_post_idx)) {
                    for ($i = 0; $i < count($pool); $i++) {
                        $pool_item = $pool[$i];
                        if ($pool_item['type'] == "POST"  && $pool_item['id'] == $top_post->id) {
                            //dd($i);
                            $pool[$i]['pts'] += (5 + $att_coefficient);
                        }
                    }
                } else {
                    array_push($pool, [
                        'id' => $top_post->id,
                        'type' => "POST",
                        'pts' => 5 + $att_coefficient
                    ]);
                    array_push($pool_post_idx, $top_post->id);
                }
            }

            $ws = WorksheetModel::where('tags', 'like', '%' . $tag . '%')->orderBy('attempts', 'inc')->get();
            //foreach ($ws as $top_ws) {
            $count_ws = count($ws);
            if ($count_ws > 10) {
                $count_ws = 10;
            }
            for ($i = 0; $i < $count_ws; $i++) { // GET THE TOP 10
                $top_ws = $ws[$i];


                $att_coefficient = round((0.3) * ($top_post->attempts));

                if (in_array($top_ws->id, $pool_ws_idx)) {
                    for ($i = 0; $i < count($pool); $i++) {
                        $pool_item = $pool[$i];
                        if ($pool_item['type'] == "WS"  && $pool_item['id'] == $top_ws->id) {
                            $pool[$i]['pts'] += (5 + $att_coefficient);
                        }
                    }
                } else {
                    array_push($pool, [
                        'id' => $top_ws->id,
                        'type' => "WS",
                        'pts' => (5 + $att_coefficient)
                    ]);
                    array_push($pool_ws_idx, $top_ws->id);
                }
            }
        }








        // LATEST MATERIAL
        foreach ($utags as $tag) {
            $posts = PostModel::where('tags', 'like', '%' . $tag . '%')->orderBy('created_at', 'inc')->get();
            //foreach ($posts as $top_post) {
            for ($i = 0; $i < 10; $i++) { // GET THE TOP 10
                $top_post = $posts[$i];


                if (in_array($top_post->id, $pool_post_idx)) {
                    for ($i = 0; $i < count($pool); $i++) {
                        $pool_item = $pool[$i];
                        if ($pool_item['type'] == "POST"  && $pool_item['id'] == $top_post->id) {
                            //dd($i);
                            $pool[$i]['pts'] += 5;
                        }
                    }
                } else {
                    array_push($pool, [
                        'id' => $top_post->id,
                        'type' => "POST",
                        'pts' => 5
                    ]);
                    array_push($pool_post_idx, $top_post->id);
                }
            }

            $ws = WorksheetModel::where('tags', 'like', '%' . $tag . '%')->orderBy('created_at', 'inc')->get();
            //foreach ($ws as $top_ws) {
            $count_ws = count($ws);
            if ($count_ws > 10) {
                $count_ws = 10;
            }
            for ($i = 0; $i < $count_ws; $i++) { // GET THE TOP 10
                $top_ws = $ws[$i];


                $att_coefficient = round((0.4) * ($top_post->attempts));

                if (in_array($top_ws->id, $pool_ws_idx)) {
                    for ($i = 0; $i < count($pool); $i++) {
                        $pool_item = $pool[$i];
                        if ($pool_item['type'] == "WS"  && $pool_item['id'] == $top_ws->id) {
                            $pool[$i]['pts'] += 5;
                        }
                    }
                } else {
                    array_push($pool, [
                        'id' => $top_ws->id,
                        'type' => "WS",
                        'pts' => 5
                    ]);
                    array_push($pool_ws_idx, $top_ws->id);
                }
            }
        }




        // SORT BY PTS
        usort($pool, function ($a, $b) {
            return $b['pts'] <=> $a['pts'];
        });

        $POOL_FINAL = array();
        $pool_count = count($pool);
        if ($pool_count > 70) {
            $pool_count = 70;
        }
        for ($k = 1; $k <= $pool_count; $k++) {
            array_push($POOL_FINAL, $pool[$k - 1]);
        }
        shuffle($POOL_FINAL);

        $request->session()->put("nf_list", json_encode($POOL_FINAL));

        //return $POOL_FINAL;
    }




    public static function newsfeed($user, $request)
    {
        //$pool = newsfeed::nfcontent($user, $request);


        /**
         * variables:
         *     nf_batch
         */

        $BATCH_SIZE = 10;

        if ($request->session()->has("nf_batch") && $request->session()->has("nf_list")) {
            $pool = json_decode($request->session()->get("nf_list"), true);
            // $request->session()->get("nf_list");

            // Get the items from the next batch,
            // increase the session variable

            $batch_no = $request->session()->get("nf_batch");

            // Check if enough posts are available
            $first = (($BATCH_SIZE) * ($batch_no)) + 1;
            $last = ($BATCH_SIZE) * ($batch_no + 1);

            if (count($pool) <= $last) {
                $request->session()->put("nf_batch", 0);
                newsfeed::nfcontent($user, $request);

                //return "<$batch_no> OUT OF BOUND";

                return newsfeed::newsfeed($user, $request);
            } else {
                $request->session()->put("nf_batch", $batch_no + 1);

                newsfeed::nfcontent($user, $request); // TEST

                //return "<$batch_no> IN BOUND, [$first-$last]";

                $NEWSFEED = array();

                for ($i = $first - 1; $i <= $last - 1; $i++) {
                    $pool_item = $pool[$i];

                    if ($pool_item['type'] == "POST") {
                        array_push($NEWSFEED, posts::get($pool_item['id']));
                    } else {
                        array_push($NEWSFEED, worksheets::get($pool_item['id']));
                    }
                }


                return json_encode($NEWSFEED);
            }


            $request->session()->put("nf_batch", $batch_no + 1);
            return $batch_no;
        } else {
            /**
             * batch number is not set. Re-create the feed.
             */

            $request->session()->put("nf_batch", 0);
            newsfeed::nfcontent($user, $request);
            return newsfeed::newsfeed($user, $request); // Recursion
        }
    }
}
