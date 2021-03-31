<?php

namespace App;

use Carbon;
use Illuminate\Support\Facades\Storage;

class activitylog
{
    /**
     * Each user has their own activitylog stored in the local storage.
     * In evets when the user posts/answers a Question/WS, follows or
     * gets followed by someone else, the info stored in it is to be updated.
     * It is stored in form of a json file with format:
     * [
     *    {
     *       datetime: "DATETIME",
     *       type: "1/2/3/4/5/6",
     *       id: "##"
     *    },
     *    {},
     *    ...
     * ]
     * 
     * Type: 1 = User has posted a Question
     * Type: 2 = User has posted a WS
     * Type: 3 = User has answered a Question
     * Type: 4 = User has answered a WS
     * Type: 5 = User has followed somebody (not yet)
     * Type: 6 = User has been followed by somebody (not yet)
     */

    public static function get($uname)
    {
        if (Storage::disk('local')->exists("actilog/{$uname}") == false) {
            Storage::put("actilog/" . $uname, "[]");
            return json_decode("[]");
        }

        return json_decode(Storage::get("actilog/" . $uname), true);
    }

    public static function put($uname, $actilog)
    {
        Storage::put("actilog/" . $uname, json_encode($actilog, true));
    }

    public static function post_question($uname, $postid)
    {
        // TYPE 1.
        $time = Carbon\Carbon::now();
        $actilog = activitylog::get($uname);
        array_push($actilog, [
            "datetime" => $time->toDateTimeString(),
            "type" => 1,
            "id" => $postid
        ]);

        activitylog::put($uname, $actilog);
    }

    public static function post_ws($uname, $wsid)
    {
        // TYPE 2.
        $time = Carbon\Carbon::now();
        $actilog = activitylog::get($uname);
        array_push($actilog, [
            "datetime" => $time->toDateTimeString(),
            "type" => 2,
            "id" => $wsid
        ]);

        activitylog::put($uname, $actilog);
    }

    public static function ans_question($uname, $postid)
    {
        // TYPE 3.
        $time = Carbon\Carbon::now();
        $actilog = activitylog::get($uname);
        array_push($actilog, [
            "datetime" => $time->toDateTimeString(),
            "type" => 3,
            "id" => $postid
        ]);

        activitylog::put($uname, $actilog);
    }

    public static function ans_ws($uname, $wsid)
    {
        // TYPE 4.
        $time = Carbon\Carbon::now();
        $actilog = activitylog::get($uname);
        array_push($actilog, [
            "datetime" => $time->toDateTimeString(),
            "type" => 4,
            "id" => $wsid
        ]);

        activitylog::put($uname, $actilog);
    }

    public static function rem_ans_ws($uname, $wsid)
    {
        // TYPE 4.
        $actilog = activitylog::get($uname);
        $actilog_new = [];
        foreach ($actilog as $acti_item) {
            if ($acti_item['id'] == $wsid && $acti_item['type'] == 4) {
                // Do nothing.
            } else {
                array_push($actilog_new, $acti_item);
            }
        }

        activitylog::put($uname, $actilog_new);
    }

    public static function rem_post_ws($uname, $wsid)
    {
        // TYPE 2.
        $actilog = activitylog::get($uname);
        $actilog_new = [];
        foreach ($actilog as $acti_item) {
            if ($acti_item['id'] == $wsid && $acti_item['type'] == 2) {
                // Do nothing.
            } else {
                array_push($actilog_new, $acti_item);
            }
        }

        activitylog::put($uname, $actilog_new);
    }
}
