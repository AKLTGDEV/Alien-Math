<?php

namespace App\classroom;


use App\CAttModel;
use App\classCollectionModel;
use App\classroom;
use App\ClassroomModel;
use App\docuploadModel;
use App\Http\Controllers\ClassroomController;
use App\NotifsModel;
use Symfony\Component\HttpFoundation\Request;
use Validator;
use Redirect;
use Illuminate\Support\Facades\Input;
use App\tags;
use App\TagsModel;
use App\UserModel;
use App\utils\randstring;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class collections
{
    public static function new(Request $request, $cid)
    {
        $class = ClassroomModel::where('id', '=', $cid)->first();
        if ($class === null) {
            return [
                'status' => "error",
                'msg'    => "Class doesn't exist",
            ];
        }

        if ($request->name == null) {
            return [
                'status' => "error",
                'msg'    => "Invalid Request",
            ];
        }

        // Create a new entry in table 'class_collections'
        $collection = new classCollectionModel;
        $collection->classid = $cid;
        $collection->name = $request->name;
        $collection->encname = randstring::generate(25);
        $collection->save();

        return [
            'status' => "ok",
            'msg'    => "Created collection '$request->name'",
        ];
    }

    public static function list($cid)
    {
        // List all the collections of this class
        $class = ClassroomModel::where('id', '=', $cid)->first();
        if ($class === null) {
            return [
                'status' => "error",
                'msg'    => "Class doesn't exist",
            ];
        }

        $collection_list = classCollectionModel::where("classid", $cid)->get();

        return $collection_list;
    }

    public static function view(Request $request, $cid, $encname)
    {
        $class = ClassroomModel::where('id', '=', $cid)->first();
        if ($class === null) {
            return abort(404);
        }

        $isadmin = false;
        if ($class->author == Auth::user()->id) {
            $isadmin = true;
        }

        if (!$isadmin) {
            return abort(403);
        }

        $collection = classCollectionModel::where("classid", $cid)
            ->where("encname", $encname)
            ->first();
        if ($collection == null) {
            return abort(404);
        }

        $dirname = $class->encname;
        $wslist = json_decode($collection->wslist, true);

        $MASTER_ATT_RATE = 0;
        $MASTER_SUCCESS_RATE = 0;
        $MASTER_FLICK = 0;
        $MASTER_TIME = 0;

        /**
         * Get the number of questions.
         */
        $MASTER_QCOUNT = 0;
        foreach ($wslist as $ws => $qlist) {
            $MASTER_QCOUNT += count($qlist);
        }


        /**
         * Prepare the "students" card
         */
        $students_card = [];
        $members = classroom::memberlist($class->id);

        foreach ($members as $m) {
            $user = UserModel::where("username", $m)->first();

            /**
             * For each student, get att the WS he/she has attempted.
             */
            $Qcount = 0;
            $correct_nos = 0;
            $flicked_nos = 0;
            $curr_att_rate = 0;
            $curr_att_nos = 0;
            $curr_success_rate = 0;
            $ws_timetaken = 0;

            foreach ($wslist as $ws => $qlist) {
                /**
                 * Check if the user has attempted this or not. If not, skip.
                 */
                $attempt = CAttModel::where("classid", $cid)
                    ->where("name", trim($ws))
                    ->where("attemptee", $user->username)
                    ->first();
                if ($attempt == null) {
                    continue;
                }

                $curr_att_nos++;

                $Qcount += count($qlist); // The number of questions. Will be required for calculating ratio.

                $ws_info = json_decode(Storage::get("classrooms/$dirname/worksheets/$ws"), true);
                $ws_stats = statistics::stats_ws_user($cid, $ws, $qlist, $user->username);

                $ws_answers = $ws_stats['answers'];
                $ws_timetaken = $ws_stats['ttaken']; // UNUSED RIGHT NOW


                //We have the WS stats. Extract info for only questions under the current collection

                $correct_nos += $ws_answers['right']; // No. of questions Answered correctly
                $flicked_nos += $ws_stats['flicked']; // No. of times the answer was flicked

                /**
                 * Get the attempt rate for each WS and update it in every iteration.
                 */
                $curr_att_rate += ((count($qlist) - $ws_answers['left']) / count($qlist));

                /**
                 * Get the success rate for each WS and update it in every iteration.
                 */
                $curr_success_rate += ($ws_answers['right'] / count($qlist));
            }

            $fin_att_rate = 0;
            $fin_success_rate = 0;

            if ($curr_att_nos != 0) {
                $fin_att_rate = $curr_att_rate / $curr_att_nos;
                $fin_success_rate = $curr_success_rate / $curr_att_nos;
            }

            array_push($students_card, [
                'name' => $user->name,
                'attempt_rate' => round($fin_att_rate * 100, 2),
                'success_rate' => round($fin_success_rate * 100, 2),
                'net_time' => $ws_timetaken,
                'flick_rate' => $flicked_nos,
            ]);

            $MASTER_ATT_RATE += $fin_att_rate;
            $MASTER_SUCCESS_RATE += $fin_success_rate;
            $MASTER_FLICK += $flicked_nos;
        }

        $MASTER_ATT_RATE /= count($members);
        $MASTER_SUCCESS_RATE /= count($members);



        /**
         * Prepare the "worksheets" card
         */

        $wscard = [];
        foreach ($wslist as $ws => $qlist) {
            $Qcount = count($qlist); // The number of questions. Will be required for calculating ratio.
            $ws_info = json_decode(Storage::get("classrooms/$dirname/worksheets/$ws"), true);
            $ws_stats = statistics::stats_ws($cid, $ws, $qlist);

            //$attemptees = $ws_stats['attemptees'];

            $ws_answers = $ws_stats['answers'];
            $ws_timetaken = $ws_stats['ttaken']; // UNUSED RIGHT NOW

            $att_rate = $ws_stats['att_rate'];
            $success_rate = $ws_stats['success_rate'];

            //We have the WS stats. Extract info for only questions under the current collection

            $correct_nos = $ws_answers['right']; // No. of questions Answered correctly
            $flicked_nos = $ws_stats['flicked']; // No. of times the answer was flicked

            array_push($wscard, [
                'name' => $ws_info['title'],
                'questions' => count($qlist),
                'attempt_rate' => round(($att_rate * 100), 2),
                'success_rate' => round(($success_rate * 100), 2),
                'net_time' => $ws_timetaken,
                'flick_rate' => $flicked_nos,
            ]);

            $MASTER_TIME += $ws_timetaken;
        }


        return view("classroom.view.collection", [
            "class" => $class,
            "collection" => $collection,
            "wsitems" => $wscard,
            "studentitems" => $students_card,
            "general" => [
                "qcount" => $MASTER_QCOUNT,
                "attempt" => round($MASTER_ATT_RATE * 100, 2),
                "success" => round($MASTER_SUCCESS_RATE * 100, 2),
                'time' => $MASTER_TIME,
                'flick' => $MASTER_FLICK,
            ],
            "isadmin" => $isadmin,
            "searchbar" => true,
        ]);
    }

    public static function rename(Request $request, $cid, $encname)
    {
        $class = ClassroomModel::where('id', '=', $cid)->first();
        if ($class === null) {
            return abort(404);
        }

        $isadmin = false;
        if ($class->author == Auth::user()->id) {
            $isadmin = true;
        }

        if (!$isadmin) {
            return abort(403);
        }

        $collection = classCollectionModel::where("classid", $cid)
            ->where("encname", $encname)
            ->first();
        if ($collection == null) {
            return abort(404);
        }

        $collection->name = $request->name;
        $collection->save();

        return redirect()->back();
    }

    public static function delete(Request $request, $cid, $encname)
    {
        $class = ClassroomModel::where('id', '=', $cid)->first();
        if ($class === null) {
            return abort(404);
        }

        $isadmin = false;
        if ($class->author == Auth::user()->id) {
            $isadmin = true;
        }

        if (!$isadmin) {
            return abort(403);
        }

        $collection = classCollectionModel::where("classid", $cid)
            ->where("encname", $encname)
            ->first();
        if ($collection == null) {
            return abort(404);
        }

        $collection->delete();

        return redirect()->route('class_stats', [$cid]);
    }
}
