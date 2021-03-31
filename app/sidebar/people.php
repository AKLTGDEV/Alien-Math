<?php

namespace App\sidebar;

use App\UserModel;

class people
{
    public static function get($user)
    {
        $start = microtime(true);
        /**
         * Return a random list of people that the user does not follow.
         * 
         * (To cut down compromise with speed)
         * (does not work, but always returns 3 people)
         */

        $ret = [];

        $people = count(UserModel::all()); // This is highly inefficient
        $upeople = json_decode($user->following, true);
        for ($i = 1; count($ret) < 3; $i++) {
            $rand = rand(1, $people);

            $u = UserModel::where("id", $rand)->first();
            if (!in_array($u->id, $upeople)) {
                array_push($ret, $u->username);
            }
        }

        \Debugbar::info("USERS " . round(microtime(true) - $start, 5));

        return [
            "flag" => true,
            "list" => $ret
        ];
    }

    public static function get_old($user)
    {
        $start = microtime(true);
        /**
         * PEOPLE TO FOLLOW
         * ----------------------------
         * Make a list of the people followed by the people user follows.
         * Select top 3 by no. of followers.
         */

        $following_level_1 = [];
        foreach (json_decode($user->following, true) as $person_id) {
            $level_1_person = UserModel::where("id", $person_id)->first();
            array_push($following_level_1, $level_1_person->username);
        }

        $level_2_users = [];
        foreach ($following_level_1 as $level1_username) {
            $level2_user_ids = json_decode(UserModel::where("username", $level1_username)->first()->following, true);
            foreach ($level2_user_ids as $l2user_id) {
                $current_l2_user = UserModel::where("id", $l2user_id)->first();
                $level_2_users[$current_l2_user->username] = $current_l2_user->nos_followers;
            }
        }

        // Weed out the people who the user already follows
        $level_2_users_final = [];
        foreach ($level_2_users as $key => $value) {
            $level_2_person = UserModel::where("username", $key)->first();
            if (in_array($level_2_person->id, json_decode($user->following, true))) {
                // This person is already followed by user. skip.
            } else {
                $level_2_users_final[$key] = $value;
            }
        }

        $list = null;
        if (count($level_2_users_final) >= 3) {
            arsort($level_2_users_final);
            $level_2_users_final = array_keys($level_2_users_final);

            $list = [
                $level_2_users_final[0],
                $level_2_users_final[1],
                $level_2_users_final[2],
            ];

            $flag = true;
        } else {
            $flag = false;
        }

        \Debugbar::info("PEOPLE " . round(microtime(true) - $start, 5));

        return [
            'flag' => $flag,
            'list' => $list,
        ];
    }
}
