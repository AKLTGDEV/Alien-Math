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
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
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

        return redirect()->route('viewpost', [$post->id]);
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
            'title'             => 'required',
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

    public function api_getinfo(Request $request, $id){
        return posts::get($id);
    }
}
