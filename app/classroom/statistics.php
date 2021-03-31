<?php

namespace App\classroom;


use App\CAttModel;
use App\ClassroomModel;
use App\docuploadModel;
use Symfony\Component\HttpFoundation\Request;
use Redirect;
use App\UserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\classroom\collections;

class statistics
{
    public static function stats_page(Request $request, $cid)
    {
        /**
         * Get the total number of questions/worksheets posted
         */
        $class = ClassroomModel::where("id", $cid)->first();
        $dirname = $class->encname;

        $nos_ws = count(Storage::allFiles("classrooms/" . $dirname . "/worksheets"));
        $nos_q = count(Storage::allFiles("classrooms/" . $dirname . "/questions"));
        $nos_q_ws = $nos_q + $nos_ws;

        $nos_q_ws_ans = CAttModel::where("classid", $class->id)->get();

        $ws_name_list = [];
        foreach (Storage::allFiles("classrooms/" . $dirname . "/worksheets") as $wsname_entry) {
            array_push($ws_name_list, explode("worksheets/", $wsname_entry)[1]);
        }
        $ws_info_list = [];
        foreach ($ws_name_list as $wsname) {
            $current_wsinfo = json_decode(Storage::get("classrooms/" . $dirname . "/worksheets//" . $wsname), true);
            array_push($ws_info_list, [
                "name" => $wsname,
                "title" => $current_wsinfo['title']
            ]);
        }

        $isadmin = false;
        if ($class->author == Auth::user()->id) {
            $isadmin = true;
        }

        // Get the Documents
        $docs = docuploadModel::where("cid", $class->id)->get();
        $docs_final = [];
        foreach ($docs as $doc) {
            array_push($docs_final, [
                "title" => $doc->title,
                "time" => $doc->time,
                "accepted" => $doc->accepted,
            ]);
        }

        //return view("classroom.statistics", [
        return view("classroom.view.stats", [
            "cid" => $cid,
            "class" => $class,
            "nos_q_ws" => $nos_q_ws,
            "nos_q_ws_ans" => count($nos_q_ws_ans),
            "worksheets" => $ws_info_list,
            "docs" => $docs_final,
            "collections" => collections::list($cid),
            "isadmin" => $isadmin,
            "searchbar" => true,
        ]);
    }

    public static function stats_reset(Request $request, $cid)
    {
        $wsname = $request->ws;
        $uname = $request->username;


        $class = ClassroomModel::where("id", $cid)->first();
        $isadmin = false;
        if ($class->author == Auth::user()->id) {
            $isadmin = true;
        }

        if ($isadmin) {
            $prevattempt = CAttModel::where("classid", $cid)
                ->where("name", $wsname)
                ->where("attemptee", $uname)
                ->first();

            if ($prevattempt == null) {
                return Redirect::to(url()->previous())->with([
                    'reset-att-status' => "danger",
                    'message' => "Attempt Not Found",
                ]);
            }

            $prevattempt->delete();

            return Redirect::to(url()->previous())->with([
                'reset-att-status' => "success",
                'message' => "Attempt removed",
            ]);
        } else {
            return Redirect::to(url()->previous())->with([
                'reset-att-status' => "danger",
                'message' => "Access Violation Error",
            ]);
        }

        return redirect()->back();
    }

    public static function stats_attemptees(Request $request, $cid, $wsname)
    {
        /**
         * return the total number of attemptees of a worksheet.
         */
        $attemptees = CAttModel::where("classid", $cid)->where("name", trim($wsname))->get();

        $ret = [];

        foreach ($attemptees as $att) {
            $att_obj = UserModel::where("username", $att->attemptee)->first();
            array_push($ret, [
                "username" => $att->attemptee,
                "name" => $att_obj->name
            ]);
        }

        return $ret;
    }

    public static function stats_userattempt(Request $request, $cid, $wsname, $uname)
    {
        $class = ClassroomModel::where("id", $cid)->first();
        $dirname = $class->encname;
        $wsinfo = json_decode(Storage::get("classrooms/" . $dirname . "/worksheets//" . trim($wsname)), true);

        $cor_answers = $wsinfo['correct'];

        $attempt = CAttModel::where("classid", $cid)->where("name", trim($wsname))->where("attemptee", $uname)->first();
        $details =  json_decode($attempt->body, true);

        $att_answers = $details['answers'];

        $right = 0;
        $wrong = 0;
        $left = 0;

        $results = array();

        for ($i = 0; $i <= count($cor_answers) - 1; $i++) {
            if ($att_answers[$i] == "N") {
                $left++;
                $results[$i] = "L"; //LEFT
            } else if ($att_answers[$i] == $cor_answers[$i]) {
                $right++;
                $results[$i] = "T"; //Correct
            } else {
                $wrong++;
                $results[$i] = "F"; //Wrong
            }
        }

        if ($right + $wrong + $left == count($cor_answers)) {
            return [
                "status" => "success",
                "general" => [
                    "wsname"  => trim($wsname),
                    "wsinfo" => $wsinfo,
                    "right" => $right,
                    "wrong" => $wrong,
                    "left" => $left
                ],
                "metrics" => $details['metrics'],
                "attempt" => $attempt,
                "answers" => $att_answers,
                "results" => $results
            ];
        } else {
            return [
                "status" => "error",
                "msg" => "..."
            ];
        }
    }




    /**
     * GET ALL STATS OF A WS FOR A COLLECTION
     */
    public static function stats_ws($cid, $wsname, $qlist)
    {

        $class = ClassroomModel::where("id", $cid)->first();
        $dirname = $class->encname;
        $wsinfo = json_decode(Storage::get("classrooms/$dirname/worksheets//" . trim($wsname)), true);

        $cor_answers = $wsinfo['correct'];

        $atts = CAttModel::where("classid", $cid)
            ->where("name", trim($wsname))
            ->get();

        $MASTER_right = 0;
        $MASTER_wrong = 0;
        $MASTER_left = 0;

        $avg_attempt_rate = 0;
        $avg_success_rate = 0;

        $MASTER_flicked = 0;

        foreach ($atts as $attempt) {

            $curr_att_rate = 0;
            $curr_success_rate = 0;

            $right = 0;
            $wrong = 0;
            $left = 0;

            $details =  json_decode($attempt->body, true);
            $att_answers = $details['answers'];


            //STEP1: TIME_TAKEN
            $net_t_taken = 0;
            foreach ($details['metrics'][0] as $q => $t_taken) {
                if (in_array($q + 1, $qlist)) {
                    // The question does belong to QList.
                    $net_t_taken += $t_taken;
                }
            }

            foreach ($details['metrics'][1] as $q => $f) {
                if (in_array($q + 1, $qlist)) {
                    // The question does belong to QList.
                    $MASTER_flicked += $f;
                }
            }

            /**
             * IN ALL ATTEMPTS, AND DATA ONLY IF THE QUESTION BELONGS TO THE QLIST
             */
            foreach ($qlist as $q) {
                $i = $q - 1;

                if ($att_answers[$i] == "N") {
                    $left++;
                } else if ($att_answers[$i] == $cor_answers[$i]) {
                    $right++;
                } else {
                    $wrong++;
                }
            }

            $curr_att_rate = ($right + $wrong) / count($qlist);
            $avg_attempt_rate += $curr_att_rate;

            $curr_success_rate = $right / count($qlist);
            $avg_success_rate += $curr_success_rate;

            $MASTER_right += $right;
            $MASTER_wrong += $wrong;
            $MASTER_left  += $left;
        }

        $avg_attempt_rate /= count($atts);
        $avg_success_rate /= count($atts);

        return [
            "status" => "success",
            "att_rate" => $avg_attempt_rate,
            "success_rate" => $avg_success_rate,
            "answers" => [
                "right" => $MASTER_right,
                "wrong" => $MASTER_wrong,
                "left" => $MASTER_left
            ],
            "ttaken" => $net_t_taken,
            "flicked" => $MASTER_flicked,
        ];
    }


    /**
     * GET ALL STATS OF A WS ATTEMPT FOR A COLLECTION
     */
    public static function stats_ws_user($cid, $wsname, $qlist, $uname)
    {

        $class = ClassroomModel::where("id", $cid)->first();
        $dirname = $class->encname;
        $wsinfo = json_decode(Storage::get("classrooms/$dirname/worksheets//" . trim($wsname)), true);

        $cor_answers = $wsinfo['correct'];

        $attempt = CAttModel::where("classid", $cid)
            ->where("name", trim($wsname))
            ->where("attemptee", $uname)
            ->first();

        $right = 0;
        $wrong = 0;
        $left = 0;

        $details =  json_decode($attempt->body, true);
        $att_answers = $details['answers'];


        //STEP1: TIME_TAKEN
        $net_t_taken = 0;
        foreach ($details['metrics'][0] as $q => $t_taken) {
            if (in_array($q + 1, $qlist)) {
                // The question does belong to QList.
                $net_t_taken += $t_taken;
            }
        }

        //STEP2: Flicked
        $flicked = 0;
        foreach ($details['metrics'][1] as $q => $f) {
            if (in_array($q + 1, $qlist)) {
                // The question does belong to QList.
                $flicked += $f;
            }
        }

        /**
         * IN ALL ATTEMPTS, AND DATA ONLY IF THE QUESTION BELONGS TO THE QLIST
         */
        foreach ($qlist as $q) {
            $i = $q - 1;

            if ($att_answers[$i] == "N") {
                $left++;
            } else if ($att_answers[$i] == $cor_answers[$i]) {
                $right++;
            } else {
                $wrong++;
            }
        }

        return [
            "status" => "success",
            "answers" => [
                "right" => $right,
                "wrong" => $wrong,
                "left" => $left
            ],
            "ttaken" => $net_t_taken,
            "flicked" => $flicked,
        ];
    }
}
