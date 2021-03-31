<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;

class testseries
{
    public static function postws($all, $encname)
    {
        $TS = TSModel::where("encname", $encname)->first();
        if ($TS == null) {
            return abort(500);
        }

        if ($TS->author != Auth::user()->id) {
            return abort(500);
        }


        $poster = Auth::user();

        $title = $all['title'];
        $nos = $all['nos'];
        $ws_name = md5($title . rand(0, 50) . rand(0, 50) . rand(0, 50));

        // STEP 1: Put all QBody's in an array.
        $qbodies = array();
        for ($i = 1; $i <= $nos; $i++) {
            array_push($qbodies, $all['Qbody-' . $i]);
        }

        // STEP 2: Put all options in an array.
        $options = array();
        for ($i = 1; $i <= $nos; $i++) {
            $o_1 = $all['option1-' . $i];
            $o_2 = $all['option2-' . $i];
            $o_3 = $all['option3-' . $i];
            $o_4 = $all['option4-' . $i];
            $O_set = [$o_1, $o_2, $o_3, $o_4];

            array_push($options, $O_set);
        }

        // STEP 3: Put all correct options in an array.
        $correct = array();
        for ($i = 1; $i <= $nos; $i++) {
            array_push($correct, $all['correct-' . $i]);
        }

        /**
         * Fix tags.
         */
        $tags = explode(",", $all['tags']);
        $tags_new = array();
        foreach ($tags as $tag) {
            $tag = trim($tag);
            $tag_entry = TagsModel::where('name', $tag)->first();
            array_push($tags_new, $tag_entry->name);
        }
        $tags = $tags_new;

        /**
         * We now have all the essential info we need.
         */

        $item_contents = [
            "datetime" => Carbon::now()->toDateTimeString(),
            "title" => $title,
            "author" => Auth::user()->username,
            "nos" => $nos,
            "bodies" => $qbodies,
            "opts" => $options,
            "correct" => $correct,
            "time" => $all['time'],
            "tags" => $tags,
        ];

        Storage::put("TS/$encname/worksheets/$ws_name", json_encode($item_contents));

        // UPDATE THE TS_INFO
        $info_items = json_decode(Storage::get("TS/$encname/info.json"), true);
        $ws_list = json_decode($info_items['wslist'], true);
        array_push($ws_list, $ws_name);
        $info_items['wslist'] = json_encode($ws_list);
        Storage::put("TS/$encname/info.json", json_encode($info_items));

        return true;
    }

    public static function preanswerws($encname, $wsname)
    {
        $TS = TSModel::where("encname", $encname)->first();

        $prevattempt = TSattempt::where("tsid", $TS->id)
            ->where("name", $wsname)
            ->where("attemptee", Auth::user()->username)
            ->first();
        if ($prevattempt != null) {
            /**
             * 
             * Let the user know that this WS has already been
             * answered by the user.
             * 
             * FIXME TODO
             */

            return redirect()->route('TSpostanswer', [$encname, $wsname]);
        } else {

            $ws_info = json_decode(Storage::get("TS/$encname/worksheets/$wsname"), true);
            $author_username = $ws_info['author'];
            $author = UserModel::where("username", $author_username)->first();

            Cookie::queue("TSwsintroseen", $wsname, 1);
            return view("TS.ws.preanswer", [
                "TS" => $TS,
                "ws" => $ws_info,
                "author" => $author,
                "wsname" => $wsname,
                "searchbar" => false,
            ]);
        }
    }

    public static function answerws($encname, $wsname)
    {
        $TS = TSModel::where("encname", $encname)->first();
        $ws_info = json_decode(Storage::get("TS/$encname/worksheets/$wsname"), true);
        $author_username = $ws_info['author'];
        $author = UserModel::where("username", $author_username)->first();


        if (Cookie::get('TSwsintroseen') != $wsname) {
            return redirect()->route('TSpreanswer', [$encname, $wsname]);
        } else {
            Cookie::queue("TSwsintroseen", null, 1);

            /**
             * 
             * Make an entry in the attempts table and return the view.
             * 
             */


            $newattempt = new TSattempt;
            $newattempt->name = $wsname;
            $newattempt->body = "[]";
            $newattempt->tsid = $TS->id;
            $newattempt->attemptee = Auth::user()->username;
            $newattempt->save();

            return view("TS.ws.answer", [
                "TS" => $TS,
                "ws" => [
                    "title" => $ws_info['title'],
                    "nos" => $ws_info['nos'],
                    "time" => $ws_info['time'],
                ],
                "author" => $author,
                "wsname" => trim($wsname),
                "searchbar" => true,
            ]);
        }
    }

    public static function pullcontentws($encname, $wsname)
    {
        $wsname = trim($wsname);
        $TS = TSModel::where("encname", $encname)->first();
        $ws_info = json_decode(Storage::get("TS/$encname/worksheets/$wsname"), true);
        $author_username = $ws_info['author'];
        $author = UserModel::where("username", $author_username)->first();

        $self = Auth::user();

        // Normalize the images
        $bodies_new = [];
        foreach ($ws_info['bodies'] as $b) {
            $b_new = str_replace('<img style=', '<img class="img-fluid" style=', $b);
            array_push($bodies_new, $b_new);
        }

        /**
         * Make sure that the WS attempt is in progress (using ws_final var.)
         */
        $catt = TSattempt::where("tsid", $TS->id)
            ->where("name", $wsname)
            ->where("attemptee", $self->username)
            ->where("ws_final", 0)
            ->first();

        if ($catt == null) {
            return [
                "status" => "error",
                "data" => []
            ];
        } else {
            return [
                "status" => "ok",
                "data" => [
                    "bodies" => $bodies_new,
                    "opts" => $ws_info['opts'],
                ]
            ];
        }
    }

    public static function answersub(Request $request, $encname, $wsname)
    {
        $recvd = $request->all();
        $self = Auth::user();

        /**
         * Check if the entry we made is here or not.
         * If yes, update it.
         */

        $wsname = trim($wsname);
        $TS = TSModel::where("encname", $encname)->first();

        $attempt = TSattempt::where("tsid", $TS->id)
            ->where("name", $wsname)
            ->where("attemptee", Auth::user()->username)
            ->first();

        if ($attempt == null) {
            return "N";
        }

        if ($attempt->ws_final == 1) {
            return "N";
        }

        $ws_info = json_decode(Storage::get("TS/$encname/worksheets/$wsname"), true);
        $author_username = $ws_info['author'];
        $author = UserModel::where("username", $author_username)->first();

        $answers = json_decode($recvd['ans'], true);

        $Ttaken = time() - strtotime($attempt->created_at);
        if ($Ttaken > ($ws_info['time']) * 60) {
            return "N";
        }

        $metrics = array();
        array_push($metrics, json_decode($recvd['clock_hits']));
        array_push($metrics, json_decode($recvd['opt_changes'])); // This is the default flicked
        //array_push($metrics, []); //Times flicked

        $attempt->body = json_encode([
            "answers" => $answers,
            "nettime" => $Ttaken,
            "metrics" => $metrics
        ]);
        $attempt->ws_final = 1;
        $attempt->save();

        return "Y";
    }

    public static function postanswerws($encname, $wsname)
    {
        $wsname = trim($wsname);
        $TS = TSModel::where("encname", $encname)->first();

        $prevattempt = TSattempt::where("tsid", $TS->id)
            ->where("name", $wsname)
            ->where("attemptee", Auth::user()->username)
            ->first();
        if ($prevattempt != null) {
            if ($prevattempt->ws_final == 1) {
                $ws_info = json_decode(Storage::get("TS/$encname/worksheets/$wsname"), true);
                $author_username = $ws_info['author'];
                $author = UserModel::where("username", $author_username)->first();

                $attempt = $prevattempt;
                $details =  json_decode($attempt->body, true);

                $att_answers = $details['answers'];

                $right = 0;
                $wrong = 0;
                $left = 0;

                $cor_answers = $ws_info['correct'];
                $results = array();

                for ($i = 0; $i <= count($cor_answers) - 1; $i++) {
                    if ($att_answers[$i] == "N") {
                        $left++;
                        //$results[$i] = "L"; //LEFT
                    } else if ($att_answers[$i] == $cor_answers[$i]) {
                        $right++;
                        //$results[$i] = "T"; //Correct
                    } else {
                        $wrong++;
                        //$results[$i] = "F"; //Wrong
                    }
                }

                return view("TS.ws.postanswer", [
                    "fucked" => false,
                    "ws" => $ws_info,
                    "author" => $author,
                    "wsname" => $wsname,
                    "results" => [
                        "right" => $right,
                        "wrong" => $wrong,
                        "left" => $left,
                        "total" => $right + $wrong + $left,
                    ],
                    "nettime" => round($details['nettime'] / 60, 3),
                    "searchbar" => false,
                ]);
            }
        } else {
            return redirect()->route('TSpreanswer', [$encname, $wsname]);
        }
    }

    public static function ws_stats($encname, $user)
    {
        /**
         * List all the worksheets in this TS
         */
        $TS = TSModel::where("encname", $encname)->first();
        $info_items = json_decode(Storage::get("TS/$encname/info.json"), true);
        $ws_list = json_decode($info_items['wslist'], true);

        $ret_list = [];

        foreach ($ws_list as $wname) {
            $ws = json_decode(Storage::get("TS/$encname/worksheets/$wname"));

            //FIXME TODO Get the users's attempt info
            $att_rate = 0;
            $success_rate = 0;
            $time = 0;
            $flick = 0;

            $user_attempt = TSattempt::where("name", $wname)
                ->where("tsid", $TS->id)
                ->where("attemptee", $user->username)
                ->first();

            if ($user_attempt != null) {
                $ws_correct = $ws->correct;
                $body = json_decode($user_attempt->body, true);

                $user_answers = $body['answers'];
                $flicked = $body['metrics'][1];

                $qcount = $ws->nos;
                $attempted_nos = 0;
                $correct_nos = 0;

                for ($i = 1; $i <= $qcount; $i++) {
                    if ($user_answers[$i - 1] != "N") {
                        // User has attempted the question
                        $attempted_nos++;

                        if ($user_answers[$i - 1] == $ws_correct[$i - 1]) {
                            // The answer is correct
                            $correct_nos++;
                        }
                    }
                }

                $att_rate = round(($attempted_nos / $qcount) * 100, 3);
                
                $success_rate = round(($correct_nos / $qcount) * 100, 3);
                
                $time = $body['nettime'];
                
                foreach ($flicked as $f) {
                    $flick += $f;
                }
            }

            $curr_wsitem = [
                "title" => $ws->title,
                "name" => $wname,
                "att_rate" => $att_rate,
                "success_rate" => $success_rate,
                "time" => $time,
                "flick" => $flick,
            ];

            array_push($ret_list, $curr_wsitem);
        }

        return $ret_list;
    }
}
