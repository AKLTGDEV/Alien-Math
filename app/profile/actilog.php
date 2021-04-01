<?php

namespace App\profile;

use App\activitylog;
use App\posts;
use App\SAQ;
use App\WorksheetModel;
use App\worksheets;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class actilog
{
    /**
     * 
     * NOTE: Under the current settings, items of same ID won't be seen together.
     * even if they have different actions. For example, if a user posts a question
     * first and then answers it, Only the latest one will be visible.
     * 
     * Change this using the $DISP_SETTINGS variable.
     * 
     */

    public static function get($user, $request)
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

        $content = actilog::get_feed_content($user, $idx);

        return [
            'result' => $content['result'],
            'idx' => $content['idx'],
        ];
    }

    public static function get_feed_content($user, $idx)
    {
        $FEED = [];
        $BATCH_LEN = 10;
        $actilog = array_reverse(activitylog::get($user->username));

        $DISP_SETTINGS = "UNIQUE"; // "UNIQUE" / "COMMON"

        $i = 1;
        while (count($FEED) < $BATCH_LEN) {

            //Check that we are in bound
            if ($i > count($actilog)) {
                break;
            }

            $actilog_item = $actilog[$i - 1];
            $type = $actilog_item['type'];
            if ($type == "1" || $type == "3") {
                $item_type = "post";
            } else if ($type == "2" || $type == "4") {
                $item_type = "WS";
            } else if ($type == "7") {
                //SAQ
                $item_type = "SAQ";
            }
            $item_id = $actilog_item['id'];

            if ($DISP_SETTINGS == "UNIQUE") {
                if (actilog::item_seen($idx, $item_type, $item_id)) {
                    $i++;
                    continue;
                }
            } else {
                if (actilog::item_seen($idx, $type, $item_id)) {
                    $i++;
                    continue;
                }
            }

            // If we are here, the item has not been seen by the user
            if ($type == "1" || $type == "3") { //POST RELATED SHIT
                $current = posts::get($actilog_item['id']);
                $current['type'] = $type;
                $current['samay'] = Carbon::parse($actilog_item['datetime'])->diffForHumans();

                if ($type == "1") {
                    $current['pretext'] = "$user->name posted this Question " . $current['samay'];
                }

                if ($type == "3") {
                    $current['pretext'] = "$user->name answered this Question " . $current['samay'];
                }

                array_push($FEED, $current);
            }
            if ($type == "2" || $type == "4") { //WS RELATED SHIT

                $current = worksheets::get($actilog_item['id']);
                $current['type'] = $type;
                $current['samay'] = Carbon::parse($actilog_item['datetime'])->diffForHumans();

                if ($type == "2") {
                    $current['pretext'] = "$user->name posted this Worksheet " . $current['samay'];
                }

                if ($type == "4") {
                    $current['pretext'] = "$user->name answered this Worksheet " . $current['samay'];
                }

                $ws_el = WorksheetModel::where("id", $actilog_item['id'])->first();
                if ($ws_el->author == Auth::user()->id) {
                    $current['mine'] = true;
                } else {
                    $current['mine'] = false;
                }

                array_push($FEED, $current);
            }
            if ($type == "7") { //SAQ
                $current = SAQ::get($actilog_item['id']);
                $current['type'] = $type;
                $current['samay'] = Carbon::parse($actilog_item['datetime'])->diffForHumans();

                if ($type == "7") {
                    $current['pretext'] = "$user->name posted this SAQ " . $current['samay'];
                }

                array_push($FEED, $current);
            }

            //Add to the IDX list
            array_push($idx, [
                "type" => $item_type,
                "id" => $item_id,
            ]);
            $i++;
        }

        return [
            "result" => $FEED,
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
