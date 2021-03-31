<?php

namespace App;

use App\PostModel;
use Carbon\Carbon;

class newsfeed
{
    public static function nf_call($user, $request)
    {
        /**
         * 
         * Whenever the "load more posts" button is clicked,
         * this function if called will all the IDs of the WS/Posts.
         * Then the result of the function below is returned as the new set of IDS.
         * 
         */

        $idx = json_decode($request->idx, true);
        //$idx = $request->idx;
        //array_push($idx, "O");

        $nf_content = newsfeed::get_nf($user, $idx);

        return [
            'result' => $nf_content['result'],
            'idx' => $nf_content['idx'],
        ];
    }

    public static function get_nf($user, $idx)
    {
        $STEP1_ADD_LIMIT = 10;
        $STEP2_ADD_LIMIT = 5;
        $STEP3_ADD_LIMIT = 5;

        $utags = json_decode(users::gettags($user->username));
        $NEWSFEED = [];


        /**
         * Step : Get the recent and most popular posts
         */
        $posts_interim = [];
        foreach ($utags as $tag) {
            $posts_usertag = PostModel::where('tags', 'like', '%' . $tag . '%')
                ->orderBy('attempts', 'inc')
                ->orderBy('id', 'desc')
                ->get();

            // Push the best 3
            /*if (count($posts_usertag) >= 1) {
                array_push($posts_interim, $posts_usertag[0]);
            }
            if (count($posts_usertag) >= 2) {
                array_push($posts_interim, $posts_usertag[1]);
            }*/
            $k = 1;
            while (count($posts_usertag) >= $k) {
                array_push($posts_interim, $posts_usertag[$k - 1]);
                $k++;
            }
        }
        shuffle($posts_interim);

        $added = 0;
        foreach ($posts_interim as $p_interim) {
            if ($added == $STEP1_ADD_LIMIT) {
                break;
            }

            if (!newsfeed::item_seen($idx, "post", $p_interim->id)) {
                // The item is not seen yet. Add it to array.
                $added++;
                array_push($NEWSFEED, posts::get($p_interim->id));

                array_push($idx, [
                    "type" => "post",
                    "id" => $p_interim->id,
                ]);
            }
        }



        /**
         * Step 2: Get the recent and most popular Worksheets
         */
        $ws_interim = [];
        foreach ($utags as $tag) {
            $ws_usertag = WorksheetModel::where('tags', 'like', '%' . $tag . '%')
                ->orderBy('attempts', 'inc')
                ->orderBy('id', 'desc')
                ->get();

            // Push the best 3
            /*if (count($ws_usertag) >= 1) {
                array_push($ws_interim, $ws_usertag[0]);
            }
            if (count($ws_usertag) >= 2) {
                array_push($ws_interim, $ws_usertag[1]);
            }*/
            $k = 1;
            while (count($ws_usertag) >= $k) {
                array_push($ws_interim, $ws_usertag[$k - 1]);
                $k++;
            }
        }
        shuffle($ws_interim);

        $added = 0;
        foreach ($ws_interim as $ws_interim_current) {
            if ($added == $STEP2_ADD_LIMIT) {
                break;
            }

            if (!newsfeed::item_seen($idx, "ws", $ws_interim_current->id)) {
                // The item is not seen yet. Add it to array.
                $added++;
                array_push($NEWSFEED, worksheets::get($ws_interim_current->id));

                array_push($idx, [
                    "type" => "ws",
                    "id" => $ws_interim_current->id,
                ]);
            }
        }



        /**
         * Step 3: Get the recent WS attempts
         */
        $wsa_interim = wsAttemptsModel::orderBy('id', 'desc')
            ->where("public", false)
            ->take($STEP3_ADD_LIMIT)
            ->get();

        $added = 0;
        foreach ($wsa_interim as $wsa_interim_current) {

            if (!$wsa_interim_current->public) {
                if (!newsfeed::item_seen($idx, "wsa", $wsa_interim_current->id)) {
                    // The item is not seen yet. Add it to array.
                    $added++;
                    $attemptee = UserModel::where("id", $wsa_interim_current->attemptee)->first();

                    if ($wsa_interim_current->attemptee != 0 && $attemptee != null) {
                        array_push($NEWSFEED, [
                            "itemT" => "wsa",
                            "username" => $attemptee->username,
                            "name" => $attemptee->name,
                            "samay" => Carbon::parse($wsa_interim_current->created_at)
                                ->diffForHumans(),
                            "fi" => worksheets::get($wsa_interim_current->wsid),
                        ]);

                        array_push($idx, [
                            "type" => "wsa",
                            "id" => $wsa_interim_current->id,
                        ]);
                    }
                }
            }
        }


        shuffle($NEWSFEED);
        return [
            "result" => $NEWSFEED,
            "idx" => $idx,
        ];
    }

    public static function item_seen($idx_list, $itemtype, $itemid)
    {
        foreach ($idx_list as $idx_item) {
            if ($idx_item['type'] == $itemtype && $idx_item['id'] == $itemid) {
                return true;
            }
        }

        return false;
    }
}
