<?php

namespace App\profile;

use App\activitylog;
use App\posts;
use App\WorksheetModel;
use App\worksheets;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class classic
{

    public static function get($user)
    {
        $toshow_actilog_items = array();

        $actilog = activitylog::get($user->username);

        foreach ($actilog as $actilog_item) {
            $type = $actilog_item['type'];

            if ($type == "1" || $type == "3") { //POST RELATED SHIT
                $current = posts::get($actilog_item['id']);
                $current['type'] = $type;
                $current['samay'] = Carbon::parse($actilog_item['datetime'])->diffForHumans();
                array_push($toshow_actilog_items, $current);
            }
            if ($type == "2" || $type == "4") { //WS RELATED SHIT
                $current = worksheets::get($actilog_item['id']);
                $current['type'] = $type;

                $ws_el = WorksheetModel::where("id", $actilog_item['id'])->first();
                if ($ws_el->author == Auth::user()->id) {
                    $current['mine'] = true;
                } else {
                    $current['mine'] = false;
                }

                $current['samay'] = Carbon::parse($actilog_item['datetime'])->diffForHumans();
                array_push($toshow_actilog_items, $current);
            }
        }

        return array_reverse($toshow_actilog_items);
    }
}
