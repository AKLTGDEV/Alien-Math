<?php

namespace App;

use Carbon\Carbon;

class notifications
{
    public static function get_content($user, $idx)
    {
        $BATCH_SIZE = 10; // Open for change
        $notifs = NotifsModel::where('for', '=', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $RET_ARRAY = [];

        foreach ($notifs as $notif) {
            $timediff = Carbon::parse($notif->created_at)->diffForHumans();

            if ($notif->type == 1) {
                // --Post--
                /**
                 * Someone the user follows has posted a question
                 */
                $post = PostModel::where('id', '=', $notif->postid)->first();
                $author = UserModel::where("id", $post->author)->first();

                if($post != null && $author != null){
                    array_push($RET_ARRAY, [
                        "type" => 1,
                        "notif_id" => $notif->id,
                        "item_id" => $post->id,
                        "msg" => "$author->name posted a Question: '$post->title'",
                        "timediff" => $timediff,
                    ]);
                }
            }
            if ($notif->type == 2) {
                // --WS--
                /**
                 * Someone the user follows has posted a WS
                 */
                $ws = WorksheetModel::where('id', '=', $notif->postid)->first();
                $author = UserModel::where("id", $ws->author)->first();

                if($ws != null && $author != null){
                    array_push($RET_ARRAY, [
                        "type" => 2,
                        "notif_id" => $notif->id,
                        "item_id" => $ws->id,
                        "msg" => "$author->name posted a Worksheet: '$ws->title'",
                        "timediff" => $timediff,
                    ]);
                }
                
            }
            if ($notif->type == 3) {
                // --WS INVITE--
                /**
                 * The user has been invited by someone to attempt a Worksheet
                 */
                /*$notif->seen = 1;
                $ws = WorksheetModel::where('id', '=', $notif->postid)->first();
                $notif->save();
                return redirect()->route('wsanswer-2', [$ws->id]);*/

                // TODO FIXME
            }
            if ($notif->type == 4) {
                // --CLS INVITE--
                /**
                 * The user has been invited by someone to join a classroom
                 */

                $class = ClassroomModel::where('id', '=', $notif->postid)->first();
                $author = UserModel::where("id", $class->author)->first();

                if($class != null && $author != null){
                    array_push($RET_ARRAY, [
                        "type" => 4,
                        "notif_id" => $notif->id,
                        "item_id" => $class->id,
                        "msg" => "$author->name Invited you to join a classroom: #$class->id",
                        "timediff" => $timediff,
                    ]);
                }
            }
        }

        return $RET_ARRAY;
    }

    public static function item_seen($idx_list, $itemid)
    {
        foreach ($idx_list as $idx_item) {
            if ($idx_item['id'] == $itemid) {
                return true;
            }
        }

        return false;
    }
}
