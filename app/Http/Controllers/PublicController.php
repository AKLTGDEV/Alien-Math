<?php

namespace App\Http\Controllers;

use App\activitylog;
use App\UserModel;
use App\utils\randstring;
use App\utils\similar_ws;
use App\WorksheetModel;
use App\worksheets;
use App\wsAttemptsModel;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class PublicController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
        // Not needed here
    }

    public function wspreanswer($slug, Request $request)
    {
        $worksheet = WorksheetModel::where('slug', $slug)->first();

        //$self = Auth::user();
        if ($worksheet != null) {
            $author = UserModel::where('id', $worksheet->author)->first();

            if (Auth::check()) {
                // User is logged in
                if (worksheets::attempted(Auth::user(), $worksheet) == true) {
                    return redirect()->route('wsanswer-3', [$worksheet->slug]);
                }
            }

            /*if (worksheets::attempted($self, $worksheet) == true) {
                return redirect()->route('wsanswer-3', [$id]);
            } else {
                return view("worksheet.answer.wsanswer-1", [
                    "ws" => $worksheet,
                    "author" => $author,
                    "searchbar" => false
                ]);
            }*/

            return view("worksheet.answer.wsanswer-1", [
                "ws" => $worksheet,
                "author" => $author,
                "META_TITLE" => $worksheet->title . " - Worksheet by @" . $author->username,
                "META_DESCRIPTION" => "worksheet hosted on CrowDoubt",
                "searchbar" => false
            ]);
        } else {
            return abort(404);
        }
    }

    public function publicresult(Request $request, $shareid)
    {
        $attempt = wsAttemptsModel::where("random_id", $shareid)->first();
        if ($attempt == null) {
            return abort(404);
        }

        $id = $attempt->wsid;
        $author = UserModel::where("id", $attempt->attemptee)->first();

        $worksheet = WorksheetModel::where('id', $id)->first();

        if ($worksheet != null) {
            if (Auth::check()) {
                $self = Auth::user();
                if (worksheets::attempted($self, $worksheet) == true) {
                    // The present user has already attempted this WS. redirect to wsdone.
                    return redirect()->route('wsanswer-3', [$worksheet->slug]);
                } else {
                    return PublicController::public_attempt_get($author->username, $id);
                }
            } else {
                return PublicController::public_attempt_get($author->username, $id);
            }
        } else {
            return abort(404);
        }
    }

    private function public_attempt_get($uname, $id)
    {
        $attemptee = UserModel::where("username", $uname)->first();
        if ($attemptee == null) {
            return abort(404);
        }

        $worksheet = WorksheetModel::where('id', $id)->first();
        $attempt = wsAttemptsModel::where('wsid', $id)->where('attemptee', $attemptee->id)->first();
        if ($attempt == null) {
            return abort(404);
        }
        $mins = ($attempt->secs) / 60;

        $stats = StatsController::stats_ws_user($id, $attemptee->username);
        $total = $stats['general']['right'] + $stats['general']['wrong'] + $stats['general']['left'];
        $right = $stats['general']['right'];

        return view("ws-stat", [
            "wsid" => $worksheet->id,
            "ws" => $worksheet->title,
            "ws_slug" => $worksheet->slug,
            "attempts" => $worksheet->attempts,
            "username" => $uname,
            "total" => $total,
            "right" => $right,
            "mins" => round($mins, 3),

            "META_TITLE" => $attemptee->name . " scored $right/$total in the test '" . $worksheet->title . "'",
            "META_DESCRIPTION" => "worksheet hosted on CrowDoubt",

            "searchbar" => false
        ]);
    }


    public function profilepic($uname)
    {
        //Check if there is even a profile picture or not
        if (Storage::disk('local')->exists("profilepx/{$uname}")) {
            return response()->download(storage_path("app/profilepx/{$uname}"));
        } else {
            return response()->download(storage_path("app/defaultprofilepic"));
        }
    }

    public function ws_public_answer(Request $request, $slug)
    {
        if (Auth::check()) {
            // Redirect to WSC
            return ['F'];
        }

        $worksheet = WorksheetModel::where('slug', $slug)->first();
        $id = $worksheet->id;

        if ($worksheet == null) {
            return abort(404);
        }

        $curr_id = "RAND_ID__" . randstring::generate(32);

        if (
            wsAttemptsModel::where('wsid', $id)
            ->where('public_id', $curr_id)
            ->first() == null
        ) {
            // We can safely assume there's been no previous attempt wit the same random ID

            $prev = url()->previous();
            $curr = url()->current();
            if ($prev != str_replace("public-answer", "preanswer", $curr)) {
                return redirect()->route('wsanswer-1', [$worksheet->slug]);
            } else {

                /**
                 * Create an entry in ws_attempts now, update when the User submits.
                 */

                $attempt = new wsAttemptsModel;
                $attempt->wsid = $id;
                $attempt->public = true;
                $attempt->attemptee = 0;
                $attempt->public_id = $curr_id;
                $attempt->save();

                //activitylog::ans_ws($self->username, $worksheet->id);

                return view("worksheet.answer.wsanswer-2", [
                    "ws" => $worksheet,
                    "public_id" => $curr_id,
                    "searchbar" => false
                ]);
            }
        } else {
            //return redirect()->route('wsanswer-3', [$worksheet->slug]);
            return ['WSANSWER_3'];

            /**
             * This shouldn't be happening. Report this incident
             */
        }
    }

    public function public_pullcontent($slug, $publicid, Request $request)
    {
        $worksheet = WorksheetModel::where('slug', $slug)->first();
        $id = $worksheet->id;

        if ($worksheet == null) {
            return abort(404);
        }

        $ws_info = json_decode(Storage::get("WS/$worksheet->ws_name"));

        $public_attempt = wsAttemptsModel::where("public_id", $publicid)->first();
        if ($public_attempt != null) {
            if ($public_attempt->answers != "[]") {
                return [
                    "status" => "error",
                    "data" => []
                ];
            }
        } else {
            return [
                "status" => "error",
                "data" => []
            ];
        }

        // Normalize the images
        $bodies_new = [];

        foreach ($ws_info->bodies as $b) {
            $b_new = str_replace('<img style=', '<img class="img-fluid" style=', $b);
            array_push($bodies_new, $b_new);
        }

        $bodies = $bodies_new;

        return [
            "status" => "ok",
            "data" => [
                "bodies" => $bodies,
                "opts" => $ws_info->opts,
            ]
        ];
    }

    public function public_answer_submit(Request $request, $publicid)
    {
        $recvd = $request->all();
        //$self = Auth::user();

        /**
         * Check if the entry we made is here or not.
         * If yes, update it.
         */

        $attempt = wsAttemptsModel::where('wsid', $recvd['wsid'])
            ->where('public_id', $publicid)
            ->first();
        if ($attempt == null) {
            return "N";
        } else {
            if ($attempt->answers != "[]") {
                return "N";
            }

            $attempt->answers = $recvd['ans'];

            $Ttaken = time() - strtotime($attempt->created_at);
            $worksheet = WorksheetModel::where('id', $recvd['wsid'])->first();
            $author = UserModel::where("id", $worksheet->author)->first();
            if ($Ttaken > ($worksheet->mins) * 60) {
                return "N";
            }
            $attempt->secs = $Ttaken;

            // The below can't be set until the user logs in

            /*$worksheet->attempts++;
            $attemptees = json_decode($worksheet->attemptees, true);
            array_push($attemptees, $self->id);
            $worksheet->attemptees = json_encode($attemptees);*/

            $metrics = array();
            array_push($metrics, json_decode($recvd['clock_hits']));
            array_push($metrics, json_decode($recvd['opt_changes']));
            array_push($metrics, []); //Times flicked

            //$attempt->metrics = json_encode($metrics);
            /**
             * Instead of saving the WS metrics on the DB, save 
             * it on local storage, and retrieve it from there.
             */
            //$attempt->metrics = "[]";
            $att_id = $attempt->wsid . "." . $attempt->public_id;
            Storage::put("wsa_metrics/" . $att_id, json_encode($metrics, true));

            // The below can't be set until the user logs in

            /*$ws_att = json_decode($self->ws_attempted, true);
            array_push($ws_att, $worksheet->id);
            $self->ws_attempted = json_encode($ws_att);*/

            $attempt->save();
            $worksheet->save();

            //$self->save();

            //rating::update($self->username);
            //rating::update($author->username);

            return "Y";
        }
    }

    public function wsdone($slug, $publicid)
    {
        $worksheet = WorksheetModel::where('slug', $slug)->first();
        $id = $worksheet->id;
        if ($worksheet == null) {
            return abort(500);
        }

        $attempt = wsAttemptsModel::where('wsid', $id)
            ->where('public_id', $publicid)
            ->first();

        if ($attempt->answers == "[]") {
            // The user fucked the Paper up.
            return view("worksheet.answer.public-wsanswer-3", [
                "ws" => $worksheet,
                "fucked" => true,
                "slug" => $slug,
                "public_id" => $publicid,
                "attempt" => $attempt,
                "searchbar" => false
            ]);
        } else {
            //Set the PUBLIC_WSATT_SLUG variable in case the user logs in
            Session::put('PUBLIC_WSATT_SLUG', $worksheet->slug);
            Session::put('PUBLIC_WSATT_PID', $publicid);

            //Get the other 3 worksheets
            $other_ws = [];
            foreach (similar_ws::get($worksheet->id, 3) as $wsid) {
                array_push($other_ws, worksheets::get($wsid));
            }

            $mins = ($attempt->secs) / 60;
            $stats = StatsController::stats_ws_publicuser($id, $publicid);
            $total = $stats['general']['right'] + $stats['general']['wrong'] + $stats['general']['left'];
            $right = $stats['general']['right'];
            return view("worksheet.answer.public-wsanswer-3", [
                "ws" => $worksheet,
                "slug" => $slug,
                "public_id" => $publicid,
                "fucked" => false,
                "attempt" => $attempt,
                "total" => $total,
                "right" => $right,
                "mins" => round($mins, 3),
                "others" => $other_ws,
                "searchbar" => false
            ]);
        }
    }
}
