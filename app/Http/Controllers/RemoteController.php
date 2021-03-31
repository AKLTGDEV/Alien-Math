<?php

namespace App\Http\Controllers;

use App\activitylog;
use App\CAttModel;
use App\classroom;
use App\ClassroomModel;
use App\newsfeed;
use App\profile\actilog;
use App\Rules\tagexists;
use App\Rules\tags_min_2;
use App\tags;
use App\TagsModel;
use App\User;
use App\UserModel;
use App\users;
use App\utils\randstring;
use App\WorksheetModel;
use App\wsAttemptsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RemoteController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }
    public function test(Request $request, $value)
    {
        return [
            "shlob" => $value,
        ];
    }

    public function req_feed(Request $request)
    {
        $user = Auth::guard('api')->user();
        return newsfeed::nf_call($user, $request);
    }

    public function listclasses(Request $request)
    {
        $user = Auth::guard('api')->user();
        $classes = json_decode($user->classrooms);

        $list = [];

        foreach ($classes as $cid) {
            $curr_class = ClassroomModel::where("id", $cid)->first();
            if ($curr_class->author == $user->id) {
                array_push($list, [
                    "name" => $curr_class->name,
                    "id" => $curr_class->id,
                    "stat" => "admin",
                    "members" => $curr_class->users,
                ]);
            } else {
                array_push($list, [
                    "name" => $curr_class->name,
                    "id" => $curr_class->id,
                    "stat" => "student",
                    "members" => $curr_class->users,
                ]);
            }
        }
        return $list;
    }

    public function profilepic(Request $request, $uname)
    {
        return [$uname];
    }

    public function classroom_postnote(Request $request, $cid)
    {
        //return ['SHLOB'];
        $class = ClassroomModel::where('id', '=', $request->id)->first();
        if ($class === null) {
            return abort(404);
        }

        classroom::postitem_note($class->id, Auth::user()->username, $request->note);

        return [
            "status" => "success"
        ];
    }

    public function publicws_getinfo(Request $request, $slug)
    {
        // Get WS Author, mins, no. of questions, title etc. by Slug

        $ws = WorksheetModel::where("slug", $slug)->first();
        if ($ws == null) {
            return [
                "status" => "error",
                "msg" => "worksheet not found"
            ];
        }

        return [
            "status" => "ok",
            "id" => $ws->id,
            "title" => $ws->title,
            "nos" => $ws->nos,
            "author" => UserModel::where("id", $ws->author)->first()->name,
            "mins" => $ws->mins,
            "attempts" => $ws->attempts,
        ];
    }

    public function publicws_interim(Request $request, $slug)
    {
        // Get WS Author, mins, no. of questions, title etc. by Slug

        $ws = WorksheetModel::where("slug", $slug)->first();
        if ($ws == null) {
            return [
                "status" => "error",
                "msg" => "worksheet not found"
            ];
        }

        if (
            wsAttemptsModel::where('wsid', $ws->id)
            ->where('attemptee', Auth::user()->id)->first() != null
        ) {
            return [
                "status" => "error",
                "msg" => "Attempt already present"
            ];
        }

        /**
         * Create an entry in ws_attempts now, update when the User submits.
         */

        $attempt = new wsAttemptsModel;
        $attempt->wsid = $ws->id;
        $attempt->attemptee = Auth::user()->id;
        $attempt->save();

        activitylog::ans_ws(Auth::user()->username, $ws->id);
        return [
            "status" => "ok",
            "msg" => "Worksheet attempt initiated"
        ];
    }

    public function publicws_postanswer(Request $request, $slug)
    {
        $ws = WorksheetModel::where("slug", $slug)->first();
        if ($ws == null) {
            return [
                "fucked" => true,
                "msg" => "worksheet not found"
            ];
        }

        $attempt = wsAttemptsModel::where('wsid', $ws->id)
            ->where('attemptee', Auth::user()->id)->first();

        if ($attempt == null) {
            return [
                "fucked" => true,
                "msg" => "Attempt not found"
            ];
        }

        if ($attempt->answers == "[]") {
            // The user fucked the Paper up.
            return [
                "fucked" => true,
                "attempt" => $attempt,
            ];
        } else {

            // Generate a randomID for sharing, in case one is not set up yet.
            if ($attempt->random_id == null) {
                //Generate one
                $attempt->random_id = randstring::generate(20);
                $attempt->save();
            }
            $shareid = $attempt->random_id;

            $mins = ($attempt->secs) / 60;

            $stats = StatsController::stats_ws_user($ws->id, Auth::user()->username);
            $total = $stats['general']['right'] + $stats['general']['wrong'] + $stats['general']['left'];
            $right = $stats['general']['right'];
            return [
                "fucked" => false,
                "attempt" => $attempt,
                "total" => $total,
                "right" => $right,
                "mins" => round($mins, 3),
                "shareid" => $shareid,
            ];
        }
    }

    public function classws_getinfo(Request $request, $cid, $encname)
    {
        // Get WS Author, mins, no. of questions, title etc. by encname
        $class = ClassroomModel::where("id", $cid)->first();
        if ($class == null) {
            return [
                "status" => "error",
                "msg" => "class not found"
            ];
        }

        $dirname = $class->encname;
        $ws = json_decode(Storage::get("classrooms/$dirname/worksheets//$encname"));

        $atts = CAttModel::where("classid", $class->id)->where("name", $encname)->get();

        return [
            "status" => "ok",
            "encname" => $encname,
            "title" => $ws->title,
            "nos" => $ws->nos,
            "author" => UserModel::where("username", $ws->author)->first()->name,
            "mins" => $ws->time,
            "attempts" => count($atts),
        ];
    }


    public function classws_interim(Request $request, $cid, $encname)
    {
        $class = ClassroomModel::where("id", $cid)->first();
        if ($class == null) {
            return [
                "status" => "error",
                "msg" => "class not found"
            ];
        }

        $dirname = $class->encname;
        $ws = json_decode(Storage::get("classrooms/$dirname/worksheets//$encname"));

        $prevatt = CAttModel::where("classid", $class->id)
            ->where("name", $encname)
            ->where("attemptee", Auth::user()->username)
            ->first();

        if ($prevatt != null) {
            return [
                "status" => "error",
                "msg" => "Attempt already present"
            ];
        }

        /**
         * Create an entry now, update when the User submits.
         */

        $attempt = new CAttModel;
        $attempt->classid = $cid;
        $attempt->name = $encname;
        $attempt->type = 2;
        $attempt->body = "[]";
        $attempt->attemptee = Auth::user()->username;
        $attempt->save();

        return [
            "status" => "ok",
            "msg" => "Worksheet attempt initiated"
        ];
    }

    public function classws_postanswer(Request $request, $cid, $encname)
    {
        $class = ClassroomModel::where("id", $cid)->first();
        if ($class == null) {
            return [
                "fucked" => true,
                "msg" => "class not found"
            ];
        }

        $dirname = $class->encname;
        $ws = json_decode(Storage::get("classrooms/$dirname/worksheets/$encname"), true);

        $attempt = CAttModel::where("classid", $class->id)
            ->where("name", $encname)
            ->where("attemptee", Auth::user()->username)
            ->first();

        if ($attempt == null) {
            return [
                "fucked" => true,
                "msg" => "Attempt not found"
            ];
        }

        if ($attempt->answers == "[]") {
            // The user fucked the Paper up.
            return [
                "fucked" => true,
                "attempt" => $attempt,
            ];
        } else {
            $details =  json_decode($attempt->body, true);
            $att_answers = $details['answers'];

            $right = 0;
            $wrong = 0;
            $left = 0;

            $cor_answers = $ws['correct'];
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

            return [
                "fucked" => false,
                "results" => [
                    "right" => $right,
                    "wrong" => $wrong,
                    "left" => $left,
                    "total" => $right + $wrong + $left,
                ],
                "nettime" => round($details['nettime'] / 60, 3),
            ];
        }
    }


    public function check_new_user(Request $request)
    {
        if (users::gettags(Auth::user()->username) == "[]") {
            return [
                "fucked" => false,
                "status" => true,
            ];
        } else {
            return [
                "fucked" => false,
                "status" => false,
            ];
        }
    }

    public function tags_top(Request $request)
    {
        return tags::top20();
    }

    public static function profile_edit_validator(Request $request)
    {
        $rules = array(
            'tags'  => ['required', 'string', new tagexists, new tags_min_2],
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            $messages = $validator->messages();
            return Redirect::to(url()->previous())
                ->withErrors($validator)
                ->withInput(Input::all());
            return [
                "fucked" => true,
                "errors" => $validator->messages(),
            ];
        } else {

            $user = Auth::user();
            $image = $request->img;
            $all = $request->all();

            if ($all['bio'] != NULL) {
                users::storebio($user->username, $all['bio']);
            }

            /**
             * For each tag, check if the user is already following it or not.
             */
            $tags_new = array();
            foreach (explode(",", $all['tags']) as $tag) {
                $tag = trim($tag);
                $tag_entry = TagsModel::where('name', $tag)->first();
                array_push($tags_new, $tag_entry->name);
            }
            $tags = $tags_new;
            $tags_old = json_decode(users::gettags($user->username), true);

            // Check if the user is following any new tags
            foreach ($tags as $ntag) {
                if (array_search($ntag, $tags_old)) {
                    // Already following this tag. Don't do shit.
                } else {
                    // Not in the old tags list. Increase the tag's following.
                    $__ntag = TagsModel::where('name', $ntag)->first();
                    $__ntag->followers++;
                    $__ntag->save();
                }
            }

            // Check if the user is unsubbing from a tag
            foreach ($tags_old as $otag) {
                if (array_search($otag, $tags)) {
                    // Already following this tag. Don't do shit.
                } else {
                    // Not in the new tags list. Decrease the tag's following.
                    $__otag = TagsModel::where('name', $otag)->first();
                    $__otag->followers--;
                    $__otag->save();
                }
            }
            users::storetags($user->username, $tags);

            if ($image == null) {
                return [
                    "fucked" => false,
                ];
            } else {
                if ($image->storeAs("profilepx/", $user->username, 'local')) {
                    return [
                        "fucked" => false,
                    ];
                } else {
                    return [
                        "fucked" => true,
                    ];
                }
            }
        }
    }

    public function new_class(Request $request)
    {
        /**
         * STEP 1: Create a DB entry.
         */

        $all = $request->all();
        $title = $all['name'];

        if($title == null){
            return [
                "fucked" => true,
            ];
        }

        $classroom = new ClassroomModel;
        $classroom->name = $title;
        $classroom->tags = "[]";
        $classroom->author = Auth::user()->id;
        $classroom->encname = md5($title . Auth::user()->username . rand(0, 100));
        $classroom->save();


        /**
         * STEP 3: Make a local storage entry
         */
        $dirname = $classroom->encname;
        Storage::makeDirectory("classrooms/" . $dirname);
        Storage::makeDirectory("classrooms/" . $dirname . "/worksheets");
        Storage::makeDirectory("classrooms/" . $dirname . "/questions");
        Storage::makeDirectory("classrooms/" . $dirname . "/notes");
        $info = [
            "name" => $title,
            "pending_invites" => "[]",
            "members" => []
        ];
        Storage::put("classrooms/" . $dirname . "/info.json", json_encode($info));
        Storage::put("classrooms/" . $dirname . "/actilog.json", "[]");

        /**
         * STEP 4: Add current user as a member,
         *         Update profile DB entry
         */
        classroom::addmember($classroom->id, Auth::user()->username);
        $self_classrooms = json_decode(Auth::user()->classrooms, true);
        array_push($self_classrooms, $classroom->id);
        Auth::user()->classrooms = json_encode($self_classrooms);
        Auth::user()->save();

        return [
            "fucked" => false,
        ];
    }


    public function int_class_auth(Request $request, $username, $cid){
        /**
         * TODO FIXME
         * 
         * Check if the user has the correct bearer token, 
         * is member of the class and is elegibel to send messages.
         * 
         */

        return [
            "fucked" => false
        ];
    }
}
