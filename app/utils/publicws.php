<?php

namespace App\utils;

use App\activitylog;
use App\rating;
use App\UserModel;
use App\WorksheetModel;
use App\wsAttemptsModel;
use Illuminate\Support\Facades\Storage;

class publicws
{
    public static function save_progress($user, $ws_slug, $pid)
    {
        $worksheet = WorksheetModel::where("slug", $ws_slug)->first();
        if ($worksheet != null) {
            $attempt = wsAttemptsModel::where("public_id", $pid)
                ->where("public", true) // Ensure that no one else comes up and fucks this up
                ->where("wsid", $worksheet->id)
                ->first();

            if ($attempt != null) {
                $worksheet->attempts++;
                $attemptees = json_decode($worksheet->attemptees, true);
                array_push($attemptees, $user->id);
                $worksheet->attemptees = json_encode($attemptees);

                if ($user->ws_attempted == null) {
                    $user->ws_attempted = json_encode([$worksheet->id]);
                } else {
                    $ws_att = json_decode($user->ws_attempted, true);
                    array_push($ws_att, $worksheet->id);
                    $user->ws_attempted = json_encode($ws_att);
                }

                $att_id = $attempt->wsid . "." . $attempt->public_id;
                $new_att_id = $attempt->wsid . "." . $user->id;
                Storage::move("wsa_metrics/$att_id", "wsa_metrics/$new_att_id");

                $attempt->public = false;
                $attempt->public_id = null;
                $attempt->attemptee = $user->id;

                $attempt->save();
                $worksheet->save();
                $user->save();

                activitylog::ans_ws($user->username, $worksheet->id);

                $author = UserModel::where("id", $worksheet->author)->first();
                rating::update($author->username);
            }
        }
    }
}
