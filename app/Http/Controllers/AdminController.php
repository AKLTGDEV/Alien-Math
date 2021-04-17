<?php

namespace App\Http\Controllers;

use App\adminlist;
use App\classroom;
use App\ClassroomModel;
use App\docuploadModel;
use App\groups;
use App\PostModel;
use App\posts;
use App\Rules\tagexists;
use App\Rules\tags_min_2;
use App\SAQ;
use App\SQA;
use App\tags;
use App\UserModel;
use App\users;
use App\WorksheetModel;
use App\worksheets;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Request;
use Validator;
use Redirect;
use TeamTNT\TNTSearch\TNTSearch;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        if (
            adminlist::isadmin(Auth::user()->username) == false
            && groups::isoperator(Auth::user()->username) == false
        ) {
            return abort(404);
        } else {

            /**
             * Get the pending Docs
             */
            $docs = docuploadModel::where("accepted", 0)->get();
            $docs_final = [];
            foreach ($docs as $doc) {
                array_push($docs_final, [
                    "id" => $doc->id,
                    "title" => $doc->title,
                    "time" => $doc->time,
                    "accepted" => $doc->accepted,
                ]);
            }

            return view("admin.admin", [
                "docs" => $docs_final,
                "all_users" => UserModel::all(),
                "searchbar" => true
            ]);
        }
    }

    public function work(Request $request)
    {
        if (!adminlist::isadmin(Auth::user()->username)) {
            return abort(404);
        } else {
            $task = $request->work;
            if ($task == "PURGE") {
                Artisan::call('cleanup'); // Remove storage directories
                Artisan::call('migrate:refresh'); // Refresh DB tables
                Artisan::call('db:seed', ['--class' => 'TagsSeeder']); // Seed Tags

                return [
                    "status" => "success",
                    "work" => "PURGE"
                ];
            } else if ($task == "TAGS.NEW") {
                tags::newtag($request->tagname);
                return [
                    "status" => "success",
                    "work" => "ADDTAG",
                    "tagname" => $request->tagname
                ];
            } else if ($task == "TAGS.DEL") {
                /**
                 * 
                 * Search all the users having this tag in their records,
                 * all questions and Worksheets, and remove them one by one.
                 * Then, delete this tag.
                 * 
                 */
                return [
                    "status" => "success",
                    "work" => "DELTAG",
                    "tagname" => $request->tagname
                ];
            }
        }
    }

    public function docindex(Request $request, $id)
    {
        if (!adminlist::isadmin(Auth::user()->username)) {
            return abort(404);
        }

        /**
         * Show info about the uploaded doc. The attatched file, the status, etc.
         */
        $doc = docuploadModel::where("id", $id)->first();
        return view("admin.docindex", [
            "doc" => $doc,
            "searchbar" => false
        ]);
        //return ['shlob'];
    }

    public function docget(Request $request, $id)
    {
        if (!adminlist::isadmin(Auth::user()->username)) {
            return abort(404);
        }

        // Get the attatched file
        $doc = docuploadModel::where("id", $id)->first();
        $enc_name = $doc->enc_name;

        if (Storage::disk('local')->exists("docs/$enc_name")) {
            return response()->download(storage_path("app/docs/$enc_name"));
        } else {
            return abort(404);
        }
    }

    public function docupload(Request $request, $id)
    {
        if (!adminlist::isadmin(Auth::user()->username)) {
            return abort(404);
        }

        $doc = docuploadModel::where("id", $id)->first();

        $class = ClassroomModel::where('id', $doc->cid)->first();
        if ($class === null) {
            return abort(404);
        }

        return view("admin.docupload", [
            "doc" => $doc,
            "class" => $class,
            "nos" => $request->nos,
            "tags_suggested" => tags::top20(),
            "title" => $doc->title,
            "mins" => $doc->time,
            "searchbar" => true,
        ]);
        //return [$request->all()];
    }

    public function doc_validate(Request $request, $id)
    {
        if (!adminlist::isadmin(Auth::user()->username)) {
            return abort(404);
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

        $validator = Validator::make($all, $rules_final);

        if ($validator->fails()) {

            //return $id . "ERR";

            //return Redirect::to(route('admindocupload', [$id]))
            return Redirect::back()
                ->withErrors($validator)
                ->withInput(Input::all());
        } else {
            //return $id . "SCC";

            $CC = new AdminController();
            return $CC->doc_submit($request, $id);
        }
    }

    public function doc_submit(Request $request, $id)
    {
        if (!adminlist::isadmin(Auth::user()->username)) {
            return abort(404);
        }

        //return $request->all();

        $doc = docuploadModel::where("id", $id)->first();
        $class = ClassroomModel::where('id', $doc->cid)->first();
        if ($class === null) {
            return abort(404);
        }

        // Mark the task done.
        $doc->accepted = 1;
        $doc->staff = Auth::user()->username;
        $doc->save();

        classroom::postitem_ws($request->id, $doc->poster, $request);
        return redirect()->route('admin');
    }

    public function composews(Request $request)
    {
        if (
            adminlist::isadmin(Auth::user()->username) == false
            && groups::isoperator(Auth::user()->username) == false
        ) {
            return abort(404);
        }

        $nos = $request->nos;
        return view("admin.ws.compose", [
            "nos" => $nos,
            "tags_suggested" => tags::top20(),
            "searchbar" => true,
        ]);
    }

    public function ws_validator(Request $request)
    {
        if (
            adminlist::isadmin(Auth::user()->username) == false
            && groups::isoperator(Auth::user()->username) == false
        ) {
            return abort(404);
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
            //return Redirect::to(url()->previous())
            return Redirect::back()
                ->withErrors($validator)
                ->withInput(Input::all());
        } else {
            $Controller = new AdminController;
            return $Controller->submit($request);
        }
    }

    public function submit(Request $request)
    {
        if (
            adminlist::isadmin(Auth::user()->username) == false
            && groups::isoperator(Auth::user()->username) == false
        ) {
            return abort(404);
        }

        $all = $request->all();
        $title = $all['title'];

        //$content = json_encode($wsitem_contents, true);
        $content = json_encode($all, true);

        $fileName = "$title.json";
        $headers = [
            'Content-type' => 'text/plain',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName),
            'Content-Length' => strlen($content)
        ];
        return Response::make($content, 200, $headers);
    }


    public function post_ws_as_user(Request $request)
    {
        if (
            adminlist::isadmin(Auth::user()->username) == false
            && groups::isoperator(Auth::user()->username) == false
        ) {
            return abort(404);
        }

        $poster_uname = $request->username;
        $poster = UserModel::where("username", $poster_uname)->first();

        $all = json_decode(file_get_contents(Input::file('data_json')->getRealPath()), true);

        return worksheets::submit($all, $poster->id);
    }

    public function exlpode_ws_as_user(Request $request)
    {
        if (
            adminlist::isadmin(Auth::user()->username) == false
            && groups::isoperator(Auth::user()->username) == false
        ) {
            return abort(404);
        }

        $poster_uname = $request->username;
        $poster = UserModel::where("username", $poster_uname)->first();
        $all = json_decode(file_get_contents(Input::file('data_json')->getRealPath()), true);

        // $all now has the WS info.
        $title = $all['title'];
        $tags = $all['tags'];
        $nos = $all['nos'];

        for ($i = 1; $i <= $nos; $i++) {
            $opt_nos = 4;
            $correct = $all["correct-$i"];
            $Qbody = $all["Qbody-$i"];

            // Post the question
            posts::newsubmit([
                "opt_nos" => $opt_nos,
                "option1" => $all["option1-$i"],
                "option2" => $all["option2-$i"],
                "option3" => $all["option3-$i"],
                "option4" => $all["option4-$i"],
                "question_tags" => $tags,
                "correct" => $correct,
                "Qbody" => $Qbody,
                "title" => $title,
            ], $poster, false);
        }

        return redirect()->back();
    }

    public function createuser(Request $request)
    {
        if (
            adminlist::isadmin(Auth::user()->username) == false
            && groups::isoperator(Auth::user()->username) == false
        ) {
            return abort(404);
        }

        $user = new UserModel;
        $user->name = $request->name;
        if ($request->email == null) {
            $user->email = rand(0, 69) . Carbon::now()->getTimestamp() . rand(0, 69) . "@xyz.com";
        } else {
            $user->email = $request->email;
        }

        if ($request->password == null) {
            $user->password = bcrypt(rand(0, 69) . $user->email . rand(0, 69));
        } else {
            $user->password = bcrypt($request->password);
        }

        if (
            $request->utype == "admin" ||
            $request->utype == "creator" ||
            $request->utype == "student"
        ) {
            $user->type = $request->utype;
        }
        $user->username = $request->username;
        $user->save();

        if ($request->bio != null) {
            users::storebio($user->username, $request->bio);
        }


        $tnt = new TNTSearch;
        $tnt->loadConfig([
            'driver'    => 'mysql',
            'host'      => config("DB_HOST"),
            'database'  => config("DB_DATABASE"),
            'username'  => config("DB_USERNAME"),
            'password'  => config("DB_PASSWORD"),
            'storage'   => storage_path('app') . "/indices//",
        ]);
        $tnt->selectIndex("users.index");
        $index = $tnt->getIndex();

        $index->insert([
            'id' => $user->id,
            'username' => $user->username,
        ]); // Not Now..

        return redirect()->back();
    }

    public function prevws(Request $request)
    {
        if (
            adminlist::isadmin(Auth::user()->username) == false
            && groups::isoperator(Auth::user()->username) == false
        ) {
            return abort(404);
        }

        $all = json_decode(file_get_contents(Input::file('data_json')->getRealPath()), true);
        $nos = $all['nos'];

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

        $ws = [
            "title" => $all['title'],
            "nos" => $nos,
            "bodies" => $qbodies,
            "opts" => $options,
            "correct" => $correct,
        ];

        return view("admin.ws.preview", [
            "ws" => $ws,
            "searchbar" => true,
        ]);
    }

    public function loginas(Request $request)
    {
        //return "Logging in as $request->username";
        Auth::logout();
        $user = UserModel::where("username", $request->username)->first();
        Auth::loginUsingId($user->id, true);

        return redirect()->route("namedprofile", $user->username);
    }

    public function post_genslug(Request $request)
    {
        // Generate Slug for posts that don't have them
        $posts = PostModel::where("slug", null)->get();

        foreach ($posts as $post) {
            $author = UserModel::where("id", $post->author)->first();
            $pretext = "$post->title Question by $author->username $post->id";

            $post->slug = str_slug($pretext);
            $post->save();
        }

        return redirect()->back();
    }

    public function ws_genslug(Request $request)
    {
        // Generate Slug for worksheets that don't have them
        $ws_list = WorksheetModel::where("slug", null)->get();

        foreach ($ws_list as $ws) {
            $author = UserModel::where("id", $ws->author)->first();
            $pretext = "$ws->title Worksheet by $author->username $ws->id";

            $ws->slug = str_slug($pretext);
            $ws->save();
        }

        return redirect()->back();
    }

    public function post_purgeslug(Request $request)
    {
        $allposts = PostModel::all();
        foreach ($allposts as $p) {
            $p->slug = null;
            $p->save();
        }

        return redirect()->back();
    }

    public function ws_purgeslug(Request $request)
    {
        $allws = WorksheetModel::all();
        foreach ($allws as $ws) {
            $ws->slug = null;
            $ws->save();
        }

        return redirect()->back();
    }

    public function jsonedit(Request $request)
    {
        // TODO
        return redirect()->back();
    }

    public function adjust_difficulties()
    {
        $qlist = [];

        foreach (PostModel::all() as $p) {
            $qlist[] = [
                "type" => "MCQ",
                "id" => $p->id,
                "rating" => $p->rating,
                "difficulty" => $p->difficulty,
            ];
        }

        foreach (SAQ::all() as $saq) {
            $qlist[] = [
                "type" => "SAQ",
                "id" => $saq->id,
                "rating" => $saq->rating,
                "difficulty" => $saq->difficulty,
            ];
        }

        foreach (SQA::all() as $sqa) {
            $qlist[] = [
                "type" => "SQA",
                "id" => $sqa->id,
                "rating" => $sqa->rating,
                "difficulty" => $sqa->difficulty,
            ];
        }

        $rating = array_column($qlist, 'rating');
        array_multisort($rating, SORT_ASC, $qlist);

        $count = count($qlist);
        $first_stop = round($count / 3);
        $second_stop = (2 * round($count / 3));

        for ($i = 1; $i <= $count; $i++) {
            $question__ = $qlist[$i - 1];
            $question = null;
            switch ($question__['type']) {
                case 'MCQ':
                    $question = PostModel::where("id", $question__['id'])->first();
                    break;

                case 'SAQ':
                    $question = SAQ::where("id", $question__['id'])->first();
                    break;

                case 'SQA':
                    $question = SQA::where("id", $question__['id'])->first();

                default:
                    // Something is wrong
                    break;
            }

            if ($i <= $first_stop) {
                // First Third
                // Difficulty = 1
                $question->difficulty = 1;
            } else {
                if ($i <= $second_stop) {
                    // Second Third
                    // Difficulty = 2
                    $question->difficulty = 2;
                } else {
                    // Third Stop
                    // Difficulty = 3
                    $question->difficulty = 3;
                }
            }

            $question->save();
        }

        return redirect()->back();
    }
}
