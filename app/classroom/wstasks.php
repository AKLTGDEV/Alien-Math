<?php

namespace App\classroom;


use App\CAttModel;
use App\classroom;
use App\ClassroomModel;
use App\docuploadModel;
use App\Http\Controllers\ClassroomController;
use App\NotifsModel;
use Symfony\Component\HttpFoundation\Request;
use Validator;
use Redirect;
use Illuminate\Support\Facades\Input;
use App\Rules\tagexists;
use App\Rules\tags_min_2;
use App\Rules\usersexist;
use App\tags;
use App\TagsModel;
use App\UserModel;
use Carbon\Carbon;
use ClassroomAttempts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class wstasks
{
    public static function postws(Request $request)
    {
        $class = ClassroomModel::where('id', '=', $request->id)->first();
        if ($class === null) {
            return abort(404);
        }

        return view("classroom.post.worksheet", [
            "class" => $class,
            "nos" => $request->nos,
            "collections" => collections::list($class->id),
            "tags_suggested" => tags::top20(),
            "searchbar" => true,
        ]);
    }

    public static function postws_validate(Request $request)
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

        //dd($rules_final);
        $validator = Validator::make($all, $rules_final);

        if ($validator->fails()) {

            return Redirect::to(url()->previous())
                ->withErrors($validator)
                ->withInput(Input::all());
        } else {
            $CC = new ClassroomController();
            return $CC->postws_submit($request);
        }
    }

    public static function postws_submit(Request $request)
    {
        /**
         * User has posted a WS.
         */

        $class = ClassroomModel::where('id', '=', $request->id)->first();
        if ($class === null) {
            return abort(404);
        }

        classroom::postitem_ws($request->id, Auth::user()->username, $request->all());

        return redirect()->route('viewclassroom', [$class->id]);
    }

    public static function postws_api(Request $request)
    {
        $class = ClassroomModel::where('id', '=', $request->id)->first();
        if ($class === null) {
            return [
                "fucked" => true,
                "msg" => "Classroom not found"
            ];
        }

        /**
         * Run Validation
         * 
         */

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
            return [
                "fucked" => true,
                "msg" => "Error processing the Worksheet.. Check your inputs",
                "errors" => $validator->messages()
            ];
        } else {
            if (classroom::postitem_ws($request->id, Auth::user()->username, $request->all())) {
                return [
                    "fucked" => false,
                    "msg" => "Worksheet posted"
                ];
            } else {
                return [
                    "fucked" => true,
                    "msg" => "Error processing the Worksheet.."
                ];
            }
        }
    }

    public static function preanswerws(Request $request, $cid, $wsname)
    {
        $prevattempt = CAttModel::where("classid", $cid)
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

            return redirect()->route('class_ws_postanswer', [$cid, $wsname]);
        } else {
            /**
             * Answer the WS.
             */

            $class = ClassroomModel::where("id", $cid)->first();
            $dirname = $class->encname;
            $ws_info = json_decode(Storage::get("classrooms/" . $dirname . "/worksheets//" . $wsname), true);

            $author_username = $ws_info['author'];
            $author = UserModel::where("username", $author_username)->first();

            Cookie::queue("wsintroseen", $wsname, 1);
            return view("classroom.ws.preanswer", [
                "cid" => $cid,
                "ws" => $ws_info,
                "author" => $author,
                "wsname" => $wsname,
                "searchbar" => false,
            ]);
        }
    }

    public static function answerws(Request $request, $cid, $wsname)
    {
        $class = ClassroomModel::where("id", $cid)->first();
        $dirname = $class->encname;
        $ws_info = json_decode(Storage::get("classrooms/" . $dirname . "/worksheets//" . $wsname), true);

        $author_username = $ws_info['author'];
        $author = UserModel::where("username", $author_username)->first();

        if (Cookie::get('wsintroseen') != $wsname) {
            return redirect()->route('class_ws_preanswer', [$cid, $wsname]);
        } else {
            Cookie::queue("wsintroseen", null, 1);

            /**
             * 
             * Make an entry in the attempts table and return the view.
             * 
             */


            $newattempt = new CAttModel;
            $newattempt->name = $wsname;
            $newattempt->type = 2; // TYPE 2 --> WS
            $newattempt->body = "[]";
            $newattempt->classid = $cid;
            $newattempt->attemptee = Auth::user()->username;
            $newattempt->save();

            return view("classroom.ws.answer", [
                "cid" => $cid,
                //"ws" => $ws_info,
                "ws" => [
                    "title" => $ws_info['title'],
                    "nos" => $ws_info['nos'],
                    "time" => $ws_info['time'],
                ],
                "author" => $author,
                "wsname" => $wsname,
                "searchbar" => true,
            ]);
        }
    }

    public static function pullcontentws(Request $request, $cid, $wsname)
    {
        $class = ClassroomModel::where("id", $cid)->first();
        $dirname = $class->encname;
        $ws_info = json_decode(Storage::get("classrooms/" . $dirname . "/worksheets//" . $wsname));

        $author_username = $ws_info->author;
        $author = UserModel::where("username", $author_username)->first();

        $self = Auth::user();

        $img_flags = [];

        // Normalize the images
        $bodies_new = [];
        $k = 1;
        foreach ($ws_info->bodies as $b) {
            if (strpos($b, '<img style=') !== false) {
                $img_flags[$k-1] = true;
            } else {
                $img_flags[$k-1] = false;
            }

            $b_new = str_replace('<img style=', '<img id="wsimg-' . $k . '" class="img-fluid" style=', $b);
            array_push($bodies_new, $b_new);

            $k++;
        }

        /**
         * Make sure that the WS attempt is in progress (using ws_final var.)
         */
        $catt = CAttModel::where("classid", $cid)
            ->where("name", $wsname)
            ->where("type", 2)
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
                    "opts" => $ws_info->opts,
                    "img_flags" => $img_flags,
                ]
            ];
        }
    }

    public static function answerwssub(Request $request)
    {
        $recvd = $request->all();
        $self = Auth::user();

        /**
         * Check if the entry we made is here or not.
         * If yes, update it.
         */

        $attempt = CAttModel::where("classid", $recvd['classid'])
            ->where("name", $recvd['wsname'])
            ->where("attemptee", Auth::user()->username)
            ->first();

        if ($attempt == null) {
            return "N";
        }

        if ($attempt->ws_final == 1) {
            return "N";
        }

        $class = ClassroomModel::where("id", $recvd['classid'])->first();
        $dirname = $class->encname;
        $ws_info = json_decode(Storage::get("classrooms/" . $dirname . "/worksheets//" . $recvd['wsname']), true);

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

    public static function postanswerws(Request $request, $cid, $wsname)
    {
        $prevattempt = CAttModel::where("classid", $cid)
            ->where("name", $wsname)
            ->where("attemptee", Auth::user()->username)
            ->first();
        if ($prevattempt != null) {
            if ($prevattempt->ws_final == 1) {
                $class = ClassroomModel::where("id", $cid)->first();
                $dirname = $class->encname;
                $ws_info = json_decode(Storage::get("classrooms/" . $dirname . "/worksheets//" . $wsname), true);

                $author_username = $ws_info['author'];
                $author = UserModel::where("username", $author_username)->first();

                $attempt = CAttModel::where("classid", $cid)->where("name", trim($wsname))->where("attemptee", Auth::user()->username)->first();
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

                return view("classroom.ws.postanswer", [
                    "cid" => $cid,
                    "ws" => $ws_info,
                    "author" => $author,
                    "wsname" => $wsname,
                    "results" => [
                        "right" => $right,
                        "wrong" => $wrong,
                        "left" => $left,
                        "total" => $right + $wrong + $left,
                    ],
                    "nettime" => $details['nettime'],
                    "searchbar" => false,
                ]);
            }
        } else {
            return redirect()->route('class_ws_preanswer', [$cid, $wsname]);
        }
    }

    public static function prevws(Request $request, $cid, $wsname)
    {
        /**
         * Make sure that it's either the class admin, or a student 
         * who has completed the test, who is requesting the preview.
         */

        $prevattempt = CAttModel::where("classid", $cid)
            ->where("name", $wsname)
            ->where("attemptee", Auth::user()->username)
            ->first();
        if ($prevattempt != null) {

            $class = ClassroomModel::where("id", $cid)->first();
            $dirname = $class->encname;
            $ws_info = json_decode(Storage::get("classrooms/" . $dirname . "/worksheets//" . $wsname), true);

            $author_username = $ws_info['author'];
            $author = UserModel::where("username", $author_username)->first();

            $att_body = json_decode($prevattempt->body, true);

            return view("classroom.ws.preview", [
                "cid" => $cid,
                "ws" => $ws_info,
                "author" => $author,
                "wsname" => $wsname,
                "answers" => $att_body['answers'],
                "corrects" => $ws_info['correct'],
                "searchbar" => true,
            ]);
        } else {
            return redirect()->route('class_ws_preanswer', [$cid, $wsname]);
        }
    }
}
