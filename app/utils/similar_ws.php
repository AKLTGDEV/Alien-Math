<?php

namespace App\utils;

use App\WorksheetModel;

class similar_ws
{
    public static function get($wsid, $nos)
    {
        /**
         * Get the ID of a public WS, and return the 
         * list of IDs of $nos similar worksheets.
         * 
         */

        $nos_ws = WorksheetModel::get()->count();

        //TODO FIXME
        $ret = [];
        //for ($i = 1; $i <= $nos; $i++) {
        while (count($ret) < $nos) {
            $r = rand(1, $nos_ws);
            if (WorksheetModel::where("id", $r)->first() != null) {
                array_push($ret, $r);
            }
        }

        return $ret;
    }
}
