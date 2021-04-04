<?php

namespace App\Http\Controllers;

use App\activitylog;
use App\NotifsModel;
use App\rating;
use App\Rules\tagexists;
use App\Rules\tags_min_2;
use App\Rules\usersexist;
use App\TagsModel;
use App\UserModel;
use App\WorksheetModel;
use App\worksheets;
use App\tags;
use App\utils\randstring;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Storage;
use App\wsAttemptsModel;
use Carbon\Carbon;
use Validator;
use Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use TeamTNT\TNTSearch\TNTSearch;

class WorksheetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function create()
    {
        return view("worksheet.create.setup", [
            "searchbar" => false
        ]);
    }

    public function compose($nos, Request $request)
    {
        $inv = $request->all()["invites"];
        if ($inv == null) {
            return view("worksheet.create.compose", [
                "nos" => $nos,
                "invites" => $inv,
                "tags_suggested" => tags::top20(),
                "searchbar" => true,
            ]);
        }

        $rules = array(
            "invites" => ['string', new usersexist]
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return Redirect::to(url()->previous())
                ->withErrors($validator)
                ->withInput(Input::all());
        } else {
            return view("worksheet.create.compose", [
                "nos" => $nos,
                "invites" => $inv,
                "tags_suggested" => tags::top20(),
                "searchbar" => true
            ]);
        }
    }

    public function validator(Request $request)
    {
        return worksheets::validator($request);
    }

    public function api_submit(Request $request)
    {
        $rules = array();
        $all = $request->all();
        $nos = $all['nos'];

        // STEP 1: Rules for Qbody
        for ($i = 1; $i <= $nos; $i++) {
            $el = array();
            $el['Qbody-' . $i] = "required";

            array_push($rules, $el);
        }

        // STEP 2: Rules for options
        for ($i = 1; $i <= $nos; $i++) {
            array_push($rules, ["option1-" . $i => "required"]);
            array_push($rules, ["option2-" . $i => "required"]);
            array_push($rules, ["option3-" . $i => "required"]);
            array_push($rules, ["option4-" . $i => "required"]);
        }

        // STEP 3: Rules for correct options
        for ($i = 1; $i <= $nos; $i++) {
            array_push($rules, ["correct-" . $i => "required"]);
        }

        $rules_final = array();
        foreach ($rules as $rule_item) {
            foreach ($rule_item as $rule_name => $rule_action) {
                $rules_final[$rule_name] = $rule_action;
            }
        }

        $rules_final['tags'] = ['required', 'string', new tagexists, new tags_min_2];

        // Do sopmething about it later
        $rules_final['title'] = ['required', 'string'];

        $validator = Validator::make($all, $rules_final);

        if ($validator->fails()) {
            return [
                "fucked" => true,
                "msg" => "Error procesing the worksheet.. Check your inputs",
                "errors" => $validator->messages(),
            ];
        } else {
            if (worksheets::submit($request->all(), Auth::user()->id)) {
                return [
                    "fucked" => false,
                    "msg" => "Worksheet posted successfully",
                ];
            } else {
                return [
                    "fucked" => true,
                    "msg" => "Unknown error occured",
                ];
            }
        }
    }

    public function preanswer($slug, Request $request)
    {
        $worksheet = WorksheetModel::where('slug', $slug)->first();

        $self = Auth::user();
        if ($worksheet != null) {
            $author = UserModel::where('id', $worksheet->author)->first();
            if (worksheets::attempted($self, $worksheet) == true) {
                return redirect()->route('wsanswer-3', [$worksheet->slug]);
            } else {
                return view("worksheet.answer.wsanswer-1", [
                    "ws" => $worksheet,
                    "author" => $author,
                    "META_TITLE" => $worksheet->title . " - Worksheet by @" . $author->username,
                    "searchbar" => false
                ]);
            }
        } else {
            return abort(404);
        }
    }

    public function answer($slug, Request $request)
    {
        $worksheet = WorksheetModel::where('slug', $slug)->first();
        $id = $worksheet->id;

        if ($worksheet == null) {
            return abort(404);
        }

        $self = Auth::user();

        if (
            wsAttemptsModel::where('wsid', $id)
            ->where('attemptee', $self->id)
            ->first() == null
        ) {
            $prev = url()->previous();
            $curr = url()->current();
            if ($prev != str_replace("answer", "preanswer", $curr)) {
                return redirect()->route('wsanswer-1', [$worksheet->slug]);
            } else {

                /**
                 * Create an entry in ws_attempts now, update when the User submits.
                 */

                $attempt = new wsAttemptsModel;
                $attempt->wsid = $id;
                $attempt->attemptee = $self->id;
                
                $attempt->save();
                Storage::put("wsa_metrics/$attempt->id/clock_hits", "[]");
                Storage::put("wsa_metrics/$attempt->id/answers", "[]");
                activitylog::ans_ws($self->username, $worksheet->id);

                return view("worksheet.answer.wsanswer-2", [
                    "ws" => $worksheet,
                    "public_id" => null,
                    "searchbar" => false
                ]);
            }
        } else {
            return redirect()->route('wsanswer-3', [$worksheet->slug]);
        }
    }

    public function pullcontent($slug, Request $request)
    {
        $worksheet = WorksheetModel::where('slug', $slug)->first();
        $id = $worksheet->id;

        if ($worksheet == null) {
            return abort(404);
        }

        $ws_info = json_decode(Storage::get("WS/$worksheet->ws_name"));
        $self = Auth::user();

        // Normalize the images
        /*$bodies_new = [];
        foreach ($ws_info->bodies as $b) {
            $b_new = str_replace('<img style=', '<img class="img-fluid" style=', $b);
            array_push($bodies_new, $b_new);
        }

        $bodies = $bodies_new;*/



        return [
            "status" => "ok",
            "data" => $ws_info,
        ];


        /**
         * FIXME: ENSURE THAT THE TEST IS ALREADY IN PROGRESS
         * 
         */
        $pending_att = wsAttemptsModel::where('wsid', $id)
            ->where('attemptee', $self->id)
            ->first();

        if ($pending_att == null) {
            return [
                "status" => "error",
                "data" => []
            ];
        }
        if ($pending_att->secs == 0) {
            /**
             * 
             * All OK. Emit the WS information
             * 
             */

            return [
                "status" => "ok",
                "data" => $ws_info,
            ];
        } else {
            return [
                "status" => "error",
                "data" => []
            ];
        }
    }

    public function answer_submit(Request $request)
    {
        $recvd = $request->all();
        $self = Auth::user();

        /**
         * Check if the entry we made is here or not.
         * If yes, update it.
         */

        $attempt = wsAttemptsModel::where('wsid', $recvd['wsid'])->where('attemptee', $self->id)->first();
        if ($attempt == null) {
            return "N";
        } else {
            $attempt->answers = $recvd['ans'];

            $Ttaken = time() - strtotime($attempt->created_at);
            $worksheet = WorksheetModel::where('id', $recvd['wsid'])->first();
            $author = UserModel::where("id", $worksheet->author)->first();
            if ($Ttaken > ($worksheet->mins) * 60) {
                return "N";
            }
            $attempt->secs = $Ttaken;
            $worksheet->attempts++;
            $attemptees = json_decode($worksheet->attemptees, true);
            array_push($attemptees, $self->id);
            $worksheet->attemptees = json_encode($attemptees);

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
            $att_id = $attempt->wsid . "." . $attempt->attemptee;
            Storage::put("wsa_metrics/" . $att_id, json_encode($metrics, true));

            $ws_att = json_decode($self->ws_attempted, true);
            array_push($ws_att, $worksheet->id);
            $self->ws_attempted = json_encode($ws_att);

            $attempt->save();
            $worksheet->save();
            $self->save();

            rating::update($self->username);
            rating::update($author->username);

            return "Y";
        }
    }

    public function done(Request $request, $slug)
    {
        if (
            Session::has('PUBLIC_WSATT_SLUG')
            && Session::has('PUBLIC_WSATT_PID')
        ) {
            $request->session()->forget([
                'PUBLIC_WSATT_SLUG',
                'PUBLIC_WSATT_PID'
            ]);
        }

        $worksheet = WorksheetModel::where('slug', $slug)->first();
        $id = $worksheet->id;
        $self = Auth::user();
        if ($worksheet != null) {
            if (worksheets::attempted($self, $worksheet) == true) {
                $attempt = wsAttemptsModel::where('wsid', $id)->where('attemptee', $self->id)->first();

                if ($attempt->answers == "[]") {
                    // The user fucked the Paper up.
                    return view("worksheet.answer.wsanswer-3", [
                        "ws" => $worksheet,
                        "fucked" => true,
                        "self" => $self,
                        "attempt" => $attempt,
                        "searchbar" => false
                    ]);
                } else {

                    // Generate a randomID for sharing, in case one is not set up yet.
                    if ($attempt->random_id == null) {
                        //Generate one
                        $attempt->random_id = randstring::generate(20);
                        $attempt->save();
                    }
                    $shareid = $attempt->random_id;

                    $mins = ($attempt->secs) / 60;

                    /*$stats = StatsController::stats_ws_user($id, $self->username);
                    $total = $stats['general']['right'] + $stats['general']['wrong'] + $stats['general']['left'];
                    $right = $stats['general']['right'];
                    return view("worksheet.answer.wsanswer-3", [
                        "ws" => $worksheet,
                        "fucked" => false,
                        "self" => $self,
                        "attempt" => $attempt,
                        "total" => $total,
                        "right" => $right,
                        "mins" => round($mins, 3),
                        "shareid" => $shareid,
                        "searchbar" => false
                    ]);*/

                    
                }
            } else {
                return redirect()->route('wsanswer-1', [$worksheet->slug]);
            }
        } else {
            return redirect()->route('wsanswer-1', [$worksheet->slug]);
        }
    }

    public function delete(Request $request, $wsid)
    {
        $self = Auth::user();

        // Check if the current user owns this WS.
        $worksheet = WorksheetModel::where('id', $wsid)->first();

        if ($worksheet->author != $self->id) {
            return abort(403);
        }

        $attemptees = json_decode($worksheet->attemptees, true);
        foreach ($attemptees as $att_id) {
            /* 
             * For each attemptee,
             * 1. remove the entry from ws_attempt table & associated wsa_metrics/ file
             * 2. remove ws_attempted entry in the users table.
             * 3. remove this item from his/her actilog.
             * 
             * */

            $metrics_file = "$wsid.$att_id";
            Storage::delete("wsa_metrics/$metrics_file");

            $attempt = wsAttemptsModel::where('wsid', $wsid)->where('attemptee', $att_id)->first();
            if ($attempt != null) {
                $attempt->delete();
            }

            $att_u = UserModel::where("id", $att_id)->first();
            $att_u_ws_attempted = json_decode($att_u->ws_attempted);
            array_splice($att_u_ws_attempted, array_search($wsid, $att_u_ws_attempted), 1);
            $att_u->ws_attempted = json_encode($att_u_ws_attempted, true);
            $att_u->save();

            activitylog::rem_ans_ws($att_u->username, $wsid);
        }

        //Delete the Local entry
        Storage::delete("WS/$worksheet->ws_name");

        activitylog::rem_post_ws(Auth::user()->username, $wsid);
        $worksheet->delete();

        return redirect()->back();
    }

    public function edit(Request $request)
    {
        $wsname = $request->wsname;
        $ws = WorksheetModel::where("ws_name", $wsname)
            ->first();

        $wsinfo = json_decode(Storage::get("WS/$wsname"));

        $nos = $ws->nos;
        $ws_opts = [];
        for ($i = 1; $i <= $nos; $i++) {
            $opts_current = $wsinfo->opts[$i - 1];

            $ws_opts["option1-$i"] = $opts_current[0];
            $ws_opts["option2-$i"] = $opts_current[1];
            $ws_opts["option3-$i"] = $opts_current[2];
            $ws_opts["option4-$i"] = $opts_current[3];
        }

        return view("worksheet.create.edit", [
            "nos" => $wsinfo->nos,
            "title" => $wsinfo->title,
            "wsname" => trim($wsname),
            "bodies" => $wsinfo->bodies,
            "options" => $ws_opts,
            "correct" => $wsinfo->correct,
            "time" => $wsinfo->time,
            "searchbar" => true,
        ]);
    }

    public function editsubmit(Request $request, $wsname)
    {
        $all = $request->all();

        /**
         * Step 1: Do some basic error checking
         * Step 2: Gather the already available data (like title, tags, etc)
         * Step 3: Combine the current data with the old data and replace the WSinfo object
         */

        // Error Checking :: FIXME

        $ws = WorksheetModel::where("ws_name", $wsname)
            ->first();
        $nos = $ws->nos;

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

        $author = UserModel::where("id", $ws->author)->first();

        $wsitem_contents = [
            "datetime" => Carbon::now()->toDateTimeString(),
            "title" => $ws->title,
            "author" => $author->username,
            "nos" => $nos,
            "bodies" => $qbodies,
            "opts" => $options,
            "correct" => $correct,
            "time" => $all['time'],
            "tags" => json_decode($ws->tags, true),
        ];

        Storage::put("WS/$wsname", json_encode($wsitem_contents));

        return redirect()->route('stats');
    }

    public function reg_attempt(Request $request, $slug, $publicid)
    {
        /**
         * When the user clicks "Login for more worksheets", this is where he lands up.
         * 
         * FIXME TODO
         */

        return redirect()->route("home");
    }
}
