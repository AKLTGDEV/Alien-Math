<?php

namespace App\Http\Controllers;

use App\activitylog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NotifsModel;
use App\PostModel;
use App\UserModel;
use App\TagsModel;
use App\posts;
use App\tags;
use App\rating;
use Illuminate\Support\Facades\Storage;
use App\Rules\tagexists;
use App\Rules\tags_min_2;
use Html2Text\Html2Text;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use League\Csv\Reader;
use TeamTNT\TNTSearch\TNTSearch;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function new()
    {
        return view("post.new", [
            "searchbar" => false,
            "tags_suggested" => tags::top20(),
        ]);
    }

    public function view($id)
    {
        if (posts::exists($id)) {
            return view("post.viewpost", [
                "post" => posts::get($id),
                "tags_suggested" => tags::top20(),
                "searchbar" => false
            ]);
        } else {
            return abort(404);
        }
    }

    public function viewimage($id)
    {
        if (posts::exists($id)) {
            $post = posts::get($id);
            if ($post['image'] != null) {
                return response()->download(storage_path("app/images/" . $post['image']));
            } else {
                return abort(404);
            }
        } else {
            return abort(404);
        }
    }

    public function newsubmit(Request $request)
    {
        $all = $request->all();

        $author = Auth::user();

        if ($request->hasFile('img')) {
            $HAS_IMAGE = true;
        } else {
            $HAS_IMAGE = false;
        }

        $post = posts::newsubmit($all, $author, $HAS_IMAGE);

        $pretext = "$post->title Question by $author->username $post->id";
        $post->slug = str_slug($pretext);
        $post->save();

        rating::update($author->username);

        $tnt = new TNTSearch;
        $tnt->loadConfig([
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', '127.0.0.1'),
            'database'  => env('DB_DATABASE', 'forge'),
            'username'  => env('DB_USERNAME', 'forge'),
            'password'  => env('DB_PASSWORD', ''),
            'storage'   => storage_path('app') . "/indices//",
        ]);
        $tnt->selectIndex("posts.index");
        $index = $tnt->getIndex();

        $index->insert([
            'id' => $post->id,
            'title' => $post->title
        ]); // Not Now..

        switch ($all['submit_mode']) {
            case 1:
                return redirect()
                    ->route('question.MCQ', [
                        $post->id
                    ]);
                break;

            case 2:
                return redirect()
                    ->route('q.gateway.add');
                break;

            default:
                return abort(501);
                break;
        }
    }

    public function answer(Request $request)
    {
        /**
         * Check if the user has already answered 
         * the question or not. if not, the the user answer.
         * Otherwise, block.
         */

        $recvd = $request->all();

        $pid = $recvd['pid'];
        $opt = $recvd['opt'];

        return posts::answer(Auth::user()->username, $pid, $opt);
    }

    public static function new_validate(Request $request)
    {
        $rules = array(
            'Qbody'             => 'required',
            //'title'             => 'required',
            'correct'           => 'required',
            'question_tags'     => ['required', 'string', new tagexists, new tags_min_2],
        );

        for ($i = 1; $i <= $request->opt_nos; $i++) {
            $rules["option$i"] = "required";
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return Redirect::to('/posts/new')
                ->withErrors($validator)
                ->withInput(Input::all());
        } else {
            $PC = new PostController();
            return $PC->newsubmit($request);
        }
    }

    public static function api_submit(Request $request)
    {
        $rules = array(
            'Qbody'             => 'required',
            'title'             => 'required',
            'correct'           => 'required',
            'question_tags'     => ['required', 'string', new tagexists, new tags_min_2],
        );

        for ($i = 1; $i <= $request->opt_nos; $i++) {
            $rules["option$i"] = "required";
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return [
                "fucked" => true,
                "msg" => "failed to process.. Check your inputs",
                "errors" => $validator->messages(),
            ];
        } else {
            // Post question

            $all = $request->all();
            $author = Auth::user();

            $post = posts::newsubmit($all, $author, false);

            if ($post != null) {
                $pretext = "$post->title Question by $author->username $post->id";
                $post->slug = str_slug($pretext);
                $post->save();

                rating::update($author->username);

                $tnt = new TNTSearch;
                $tnt->loadConfig([
                    'driver'    => 'mysql',
                    'host'      => env('DB_HOST', '127.0.0.1'),
                    'database'  => env('DB_DATABASE', 'forge'),
                    'username'  => env('DB_USERNAME', 'forge'),
                    'password'  => env('DB_PASSWORD', ''),
                    'storage'   => storage_path('app') . "/indices//",
                ]);
                $tnt->selectIndex("posts.index");
                $index = $tnt->getIndex();

                $index->insert([
                    'id' => $post->id,
                    'title' => $post->title
                ]); // Not Now..


                return [
                    "fucked" => false,
                    "msg" => "Question posted successfully",
                ];
            } else {
                return [
                    "fucked" => true,
                    "msg" => "Unknown error occured",
                ];
            }
        }
    }

    public function api_getinfo(Request $request, $id)
    {
        return posts::get($id);
    }

    public function edit($id)
    {
        $question = PostModel::where("id", $id)->first();

        if ($question != null) {
            $opts = json_decode($question->opts);

            return view("post.edit", [
                "searchbar" => false,
                "question" => $question,
                "tags_suggested" => tags::top20(),
                "opts" => $opts,
            ]);
        } else {
            return abort(404);
        }
    }

    public function edit_submit(Request $request, $id)
    {
        $question = PostModel::where("id", $id)->first();

        if ($question != null) {

            /***
             * 
             * Error Checking :: FIXME
             * 
             */

            $all = $request->all();
            $author = Auth::user();

            $post = $question;

            //$options = [$all['option1'], $all['option2']];
            $options = [];
            for ($i = 1; $i <= $all['opt_nos']; $i++) {
                array_push($options, $all['option' . $i]);
            }

            $options = json_encode($options);

            $tags = explode(",", $all['question_tags']);
            $tags_new = array();
            foreach ($tags as $tag) {
                $tag = trim($tag);
                $tag_entry = TagsModel::where('name', $tag)->first();
                array_push($tags_new, $tag_entry->name);
            }
            $tags = $tags_new;

            $correct_opt = $all['correct'];

            $author->nos_Q++;
            $author->save();
            $post->opts = $options;
            $post->correctopt = $correct_opt;
            $post->tags = json_encode($tags);
            $post->type = $all['grade'];
            $post->difficulty = $all['difficulty'];
            $digest = new Html2Text($all['Qbody']);
            $digest = $digest->getText();
            $digest = str_replace("_", " ", $digest);
            $digest = strtolower($digest);
            //$post->title = $all['title'];
            $post->title = $digest;

            Storage::put("posts/" . $post->text, $all['Qbody']);

            $post->save(); //Hopefully.

            $post->saveExplanation($request->explanation);

            $tnt = new TNTSearch;
            $tnt->loadConfig([
                'driver'    => 'mysql',
                'host'      => env('DB_HOST', 'localhost'),
                'database'  => env('DB_DATABASE', ''),
                'username'  => env('DB_USERNAME', ''),
                'password'  => env('DB_PASSWORD', ''),
                'storage'   => storage_path('app') . "/indices//",
            ]);

            $tnt->selectIndex("posts.index");
            $index = $tnt->getIndex();

            $index->update($post->id, [
                'id' => $post->id,
                'title' => $post->title,
            ]);

            return redirect()->route("question.MCQ", [
                $post->id
            ]);
        } else {
            return abort(404);
        }
    }

    public function upload()
    {
        return view("post.upload", [
            "searchbar" => false,
            "tags_suggested" => tags::top20(),
        ]);
    }

    public function upload_validate(Request $request)
    {
        $author = Auth::user();
        if ($request->file('csv')->isValid()) {
            $csv_uploaded = $request->file('csv')->path();

            //load the CSV document from a file path
            $csv = Reader::createFromPath($csv_uploaded, 'r');
            $csv->setHeaderOffset(0);

            $header = $csv->getHeader(); //returns the CSV header record
            $records = $csv->getRecords(); //returns all the CSV records as an Iterator object

            $count = 0;
            foreach ($records as $record) {
                //Each row is a seperate Question.
                $q = [
                    "Qbody" => $record['question'],
                    "option1" => $record['O1'],
                    "option2" => $record['O2'],
                    "option3" => $record['O3'],
                    "option4" => $record['O4'],
                    "correct" => $record['correct'],
                    "grade" => $record['grade'],
                    "difficulty" => $record['difficulty'],
                    "question_tags" => $record['tags'],
                    "opt_nos" => 4,
                    "title" => "...", //TODO
                ];

                if (isset($record['explanation'])) {
                    $q["explanation"] = $record['explanation'];
                }

                $post = posts::newsubmit($q, $author, false);

                $pretext = "$post->title MCQ by $author->username $post->id";
                $post->slug = str_slug($pretext);
                $post->save();

                rating::update($author->username);

                $tnt = new TNTSearch;
                $tnt->loadConfig([
                    'driver'    => 'mysql',
                    'host'      => env('DB_HOST', '127.0.0.1'),
                    'database'  => env('DB_DATABASE', 'forge'),
                    'username'  => env('DB_USERNAME', 'forge'),
                    'password'  => env('DB_PASSWORD', ''),
                    'storage'   => storage_path('app') . "/indices//",
                ]);
                $tnt->selectIndex("posts.index");
                $index = $tnt->getIndex();

                $index->insert([
                    'id' => $post->id,
                    'title' => $post->title
                ]); // Not Now..

                $count++;
            }

            return Redirect::to(route('namedprofile', [$author->username]))->with([
                "status" => "success",
                "message" => $count . " MCQs Uploaded",
            ]);
        }
    }
}
