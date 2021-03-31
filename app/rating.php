<?php

namespace App;

use App\Http\Controllers\StatsController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class rating
{
    public static function update($uname)
    {
        /**
         *            RATING SYSTEM:
         * 
         * ** User posts a question                     ===> +1
         * ** User answers a question (right answer)    ===> +3
         * ** User answers a question (wrong answer)    ===> -3
         * 
         * ** Someone answers user's question           ===> +5
         * ** User posts a WS                           ===> +3
         * ** User answers someone else's WS            ===> +4
         * ** Someone answers user's WS                 ===> +8
         * 
         * ** Someone follows user                      ===> +2
         * 
         * ** FOR EACH WS:
         * ** *** Right Answer: +5
         * ** *** Wrong Answer: -5
         * ** *** Left: -1
         * 
         */

        $user = UserModel::where("username", $uname)->first();

        /**
         * Get the total number of questions posted
         */
        $q_posted = $user->nos_Q;

        /**
         * No. of questions answered correctly, wrongly
         */
        $answers_t = $user->answers_t;
        $answers_f = $user->answers_f;

        /**
         * get the total attemptees of questions posted by user
         */
        $total_attemptees = 0;
        $posts = PostModel::where("author", $user->id)->get();
        foreach ($posts as $post) {
            $total_attemptees += $post->attempts;
        }

        /**
         * Get the total number of WS posted by the user
         */
        $total_ws = count(json_decode($user->ws_posted, true));

        /**
         * Get the number of WS attempts made by user
         */
        $ws_attempt_self = count(json_decode($user->ws_attempted, true));

        /**
         * Collect WS data
         */
        $grand_ws_right = 0;
        $grand_ws_wrong = 0;
        $grand_ws_left = 0;

        $ws_att_list = json_decode($user->ws_attempted, true);
        foreach ($ws_att_list as $wsid) {
            $curr_stats = StatsController::stats_ws_user($wsid, $user->username);

            $curr_right = $curr_stats['general']['right'];
            $curr_wrong = $curr_stats['general']['wrong'];
            $curr_left = $curr_stats['general']['left'];

            $grand_ws_right += $curr_right;
            $grand_ws_wrong += $curr_wrong;
            $grand_ws_left += $curr_left;
        }

        //dd([$grand_ws_right, $grand_ws_wrong, $grand_ws_left]);


        /**
         * Get the total number of attempts on user's worksheets
         */
        $ws_attempt_others = 0;
        $user_ws_list = WorksheetModel::where("author", $user->id)->get();
        foreach ($user_ws_list as $ws) {
            $attempts = wsAttemptsModel::where("wsid", $ws->id)->get();
            $ws_attempt_others += count($attempts);
        }

        /**
         * Get the number of followers of the user;
         */
        $followers = $user->nos_followers;

        // CALCULATE THE RATING
        
        $rating =
            ($q_posted * 1) +
            ($answers_t * 3) -
            ($answers_f * 3) +
            ($total_attemptees * 5) +
            ($total_ws * 3) +
            ($ws_attempt_self * 4) +
            ($ws_attempt_others * 8) +
            ($followers * 2) +
            
            ($grand_ws_right * 5) -
            ($grand_ws_wrong * 3) -
            ($grand_ws_left * 1);

        $user->rating = $rating;
        $user->save();

        /**
         * Save the current day's record at dailyrecord/{uname}
         */
        $today = Carbon::now()->toDateString();
        $dr = rating::get_dr($user);
        $dr[$today] = $rating;
        rating::store_dr($user, $dr);        
    }

    public static function get_dr($user) {
        if (Storage::disk('local')->exists("dailyrecord/{$user->username}") == false) {
            //Just making sure
            Storage::put("dailyrecord/" . $user->username, "[]");
            return [];
        } else {
            return json_decode(Storage::get("dailyrecord/" . $user->username), true);
        }
    }

    public static function store_dr($user, $dr) {
        Storage::put("dailyrecord/" . $user->username, json_encode($dr, true));
    }
}
