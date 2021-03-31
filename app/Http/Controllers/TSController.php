<?php

namespace App\Http\Controllers;

use App\classroom\other;
use App\payments;
use App\Rules\tagexists;
use App\Rules\tags_min_2;
use App\tags;
use App\TagsModel;
use App\testseries;
use App\TSattempt;
use App\TSModel;
use App\UserModel;
use App\utils\randstring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TSController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function new(Request $request)
    {
        return view("TS.new", [
            "tags_suggested" => tags::top20(),
            "searchbar" => false
        ]);
    }

    public function newsubmit(Request $request)
    {
        $all = $request->all();
        $title = $all['name'];

        if ($all['tags'] != null) {
            $tags = explode(",", $all['tags']);
            $tags_new = array();
            foreach ($tags as $tag) {
                $tag = trim($tag);
                $tag_entry = TagsModel::where('name', $tag)->first();
                array_push($tags_new, $tag_entry->name);
            }
            $tags = $tags_new;
        } else {
            $tags = [];
        }

        $TS = new TSModel;
        $TS->name = $title;
        $TS->tags = json_encode($tags);
        $TS->author = Auth::user()->id;
        $TS->amount = $all['price'];
        $TS->nos = 0;

        $encname = randstring::generate(25);
        while (TSModel::where("encname", $encname)->first() != null) {
            $encname = randstring::generate(25);
        }
        $TS->encname = $encname;
        $TS->save();


        /**
         * STEP 3: Make a local storage entry
         */
        $dirname = $TS->encname;
        Storage::makeDirectory("TS/" . $dirname);
        Storage::makeDirectory("TS/$dirname/worksheets");
        $info = [
            "name" => $title,
            "students" => "[]", //The list of students who have bought this TS
            "wslist" => "[]", // List of names of WS present in ./worksheets/*
        ];
        Storage::put("TS/$dirname/info.json", json_encode($info));

        $ts_created_list = json_decode(Auth::user()->ts_created, true);
        array_push($ts_created_list, $TS->id);
        Auth::user()->ts_created = json_encode($ts_created_list, true);
        Auth::user()->save();

        //Redirect to the index of the TS
        return redirect()->route('TSindex', [$encname]);
    }

    public function index(Request $request, $encname)
    {
        $TS = TSModel::where("encname", $encname)->first();

        $author = false;
        if ($TS->author == Auth::user()->id) {
            $author = true;
        }

        $author__ = UserModel::where("id", $TS->author)->first();
        $info_items = json_decode(Storage::get("TS/$encname/info.json"), true);

        // Check if current user is a member of this series or not.
        if (!$author) {
            $students = json_decode($info_items['students'], true);
            if (!in_array(Auth::user()->username, $students)) {
                // Not a member.

                $order = payments::razorpay_order($TS->amount);

                return view("TS.outsider", [
                    "TS" => $TS,
                    "author" => $author__,
                    "info" => $info_items,
                    "order" => $order,
                    "user" => Auth::user(),
                    "searchbar" => false
                ]);
            }
        }

        $ws_list = json_decode($info_items['wslist'], true);

        $WS_coll = [];
        $app_url = Config::get('app.url');

        foreach ($ws_list as $wname) {
            $ws = json_decode(Storage::get("TS/$encname/worksheets/$wname"));

            array_push($WS_coll, [
                'itemT' => 'ws',
                'wsname' => $wname,
                'title' => $ws->title,
                'attempts' => 0,
                'nos' => $ws->nos,
                'mins' => $ws->time,
                'name' => $author__->name,
                'username' => $author__->username,
                'tags' => $ws->tags,
                'profilepic' => "{$app_url}/user/{$author__->username}/profilepic",
                'attempted' => false
            ]);
        }

        return view("TS.index", [
            "TS" => $TS,
            "author" => $author,
            "wslist" => $WS_coll,
            "searchbar" => true
        ]);
    }

    public function compose(Request $request, $encname)
    {
        $TS = TSModel::where("encname", $encname)->first();

        $author = false;
        if ($TS->author == Auth::user()->id) {
            $author = true;
        }

        return view("TS.compose", [
            "TS" => $TS,
            "nos" => $request->nos,
            "tags_suggested" => tags::top20(),
            "searchbar" => false
        ]);
    }

    public function validator(Request $request, $encname)
    {
        $TS = TSModel::where("encname", $encname)->first();

        if ($TS == null) {
            return abort(500);
        }

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

        //dd($rules_final);
        $validator = Validator::make($all, $rules_final);

        if ($validator->fails()) {

            return Redirect::to(url()->previous())
                ->withErrors($validator)
                ->withInput(Input::all());
        } else {
            //return $all;
            if (testseries::postws($all, $encname)) {
                return redirect()->route('TSindex', [$encname]);
            } else {
                return abort(500);
            }
        }
    }

    public function preanswer(Request $request, $encname, $wsname)
    {
        return testseries::preanswerws($encname, $wsname);
    }

    public function answer(Request $request, $encname, $wsname)
    {
        return testseries::answerws($encname, $wsname);
    }

    public function postanswer($encname, $wsname)
    {
        return testseries::postanswerws($encname, $wsname);
    }

    public function pullcontent($encname, $wsname)
    {
        return testseries::pullcontentws($encname, $wsname);
    }

    public function answersub(Request $request, $encname, $wsname)
    {
        return testseries::answersub($request, $encname, $wsname);
    }

    public function stats(Request $request, $encname)
    {
        $TS = TSModel::where("encname", $encname)->first();

        if ($TS == null) {
            return abort(500);
        }

        $u = UserModel::where("username", $request->u)->first();
        $wslist = testseries::ws_stats($encname, $u);

        //return $wslist;

        return view("TS.stats", [
            "TS" => $TS,
            "user" => $u,
            "wslist" => $wslist,
            "searchbar" => false
        ]);
    }

    public function stats_ws(Request $request, $encname, $wsname)
    {
        //return [$encname, $wsname];
        // return the list of attemptees
        $TS = TSModel::where("encname", $encname)->first();

        $attempts = TSattempt::where("tsid", $TS->id)
            ->where("name", trim($wsname))
            ->get();

        $ret = [];

        foreach ($attempts as $att) {
            $att_obj = UserModel::where("username", $att->attemptee)->first();
            array_push($ret, [
                "username" => $att->attemptee,
                "name" => $att_obj->name
            ]);
        }

        return $ret;
    }

    public function stats_ws_u(Request $request, $encname, $wsname, $uname)
    {
        $TS = TSModel::where("encname", $encname)->first();
        $wsinfo = json_decode(Storage::get("TS/$encname/worksheets/$wsname"), true);

        $cor_answers = $wsinfo['correct'];

        $attempt = TSattempt::where("tsid", $TS->id)
            ->where("name", trim($wsname))
            ->where("attemptee", $uname)
            ->first();
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

    public function settings(Request $request, $encname)
    {
        $TS = TSModel::where("encname", $encname)->first();

        if ($TS == null) {
            return abort(500);
        }

        if ($TS->author != Auth::user()->id) {
            return abort(403);
        }

        $info_items = json_decode(Storage::get("TS/$encname/info.json"), true);

        $nos_students = count(json_decode($info_items['students'], true));

        $earned_total = ($TS->amount * $nos_students) * (9 / 10);
        return view("TS.settings", [
            "TS" => $TS,
            "info" => $info_items,
            "nos_students" => $nos_students,
            "earned_total" => $earned_total,
            "searchbar" => false
        ]);
    }
}
