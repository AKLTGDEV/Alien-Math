<?php

namespace App\Http\Controllers;

use App\tags;
use App\TagsModel;
use App\TagRequestsModel;
use App\users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class TagsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function view($tag)
    {
        $user = Auth::user();

        /**
         * Check if the tag even exists or not.
         */

        if (tags::exists($tag)) {
            $tag_obj = TagsModel::where("name", $tag)->first();

            /**
             * Check if the user is following the current tag or not
             */
            $u_tags = json_decode(users::gettags($user->username), true);
            $following_flag = false;
            foreach ($u_tags as $tag_single) {
                if ($tag_single == $tag) {
                    $following_flag = true;
                }
            }

            /**
             * Get the total number of posts, Worksheets, and
             * followers of the current tag.
             * 
             * TODO IMPROVEMENT: A GitHub-like graph of activity
             * 
             */

            return view("post.tagposts", [
                "posts" => tags::allposts($tag), // FIXME, only relatable posts
                "tag" => $tag,
                "carousel" => false,
                "following" => $following_flag,
                "nos_posts" => count(tags::allposts($tag)),
                "nos_worksheets" => 0,
                "nos_followers" => $tag_obj->followers,

                "searchbar" => true
            ]);
        } else {
            return abort(404);
        }
    }

    public static function gather(Request $request, $tag)
    {
        return tags::gather($request, $tag);
    }

    public static function basicinfo(Request $request, $tag)
    {
        if (tags::exists($tag)) {
            /**
             * Check if the user is following the current tag or not
             */
            $u_tags = json_decode(users::gettags(Auth::user()->username), true);
            $following_flag = false;
            foreach ($u_tags as $tag_single) {
                if ($tag_single == $tag) {
                    $following_flag = true;
                }
            }

            return [
                "fucked" => false,
                "following" => $following_flag,
            ];
        } else {
            return [
                "fucked" => true,
                "msg" => "tag does not exist"
            ];
        }
    }

    public function follow($tag)
    {
        /**
         * 
         * FIXME: TRIM TAGS FOR SAFETY
         * 
         */

        $user = Auth::user();
        $u_tags = json_decode(users::gettags($user->username), true);

        foreach ($u_tags as $tag_single) {
            if ($tag == $tag_single) {
                //Already following
                return Redirect::to(url()->previous());
            }
        }

        array_push($u_tags, $tag);
        users::storetags($user->username, $u_tags);

        tags::tagfollower_new($tag);

        return Redirect::to(url()->previous());
    }

    public function follow_api($tag)
    {
        $user = Auth::user();
        $u_tags = json_decode(users::gettags($user->username), true);

        foreach ($u_tags as $tag_single) {
            if ($tag == $tag_single) {
                //Already following
                return [
                    "fucked" => false,
                    "msg" => "Already Following Topic $tag"
                ];
            }
        }

        array_push($u_tags, $tag);
        users::storetags($user->username, $u_tags);
        tags::tagfollower_new($tag);

        return [
            "fucked" => false,
            "msg" => "Following Topic $tag"
        ];
    }

    public function unfollow($tag)
    {
        /**
         * 
         * FIXME: TRIM TAGS FOR SAFETY
         * 
         */

        $user = Auth::user();
        $u_tags = json_decode(users::gettags($user->username), true);

        $u_tags_final = array();
        foreach ($u_tags as $tag_single) {
            if ($tag == $tag_single) {
                //Currently following, don't copy this
            } else {
                array_push($u_tags_final, $tag_single);
            }
        }

        users::storetags($user->username, $u_tags_final);

        tags::tagfollower_rem($tag);

        return Redirect::to(url()->previous());
    }

    public function unfollow_api($tag)
    {
        /**
         * 
         * FIXME: TRIM TAGS FOR SAFETY
         * 
         */

        $user = Auth::user();
        $u_tags = json_decode(users::gettags($user->username), true);

        $u_tags_final = array();
        foreach ($u_tags as $tag_single) {
            if ($tag == $tag_single) {
                //Currently following, don't copy this
            } else {
                array_push($u_tags_final, $tag_single);
            }
        }

        users::storetags($user->username, $u_tags_final);
        tags::tagfollower_rem($tag);

        return [
            "fucked" => false,
            "msg" => "Unfollowed Topic $tag"
        ];
    }

    public function top20()
    {
        return tags::top20();
    }

    public function request(Request $request)
    {
        if (Auth::user()->rating < 100) {
            return view("newtopic", [
                "eligible" => false,
                "searchbar" => false,
                "rating" => Auth::user()->rating
            ]);
        } else {

            /**
             * Return the most popular topic requests from the 
             * DB, both accepted and rejected ones.
             * 
             * FIXME TODO
             */

            $tagreqs_final = [];
            $tagreqs = TagRequestsModel::all();
            for ($i = 0; $i < count($tagreqs); $i += 3) {
                $row = [];
                if (count($tagreqs) >= $i + 1) {
                    array_push($row, $tagreqs[$i]);
                }

                if (count($tagreqs) >= $i + 2) {
                    array_push($row, $tagreqs[$i + 1]);
                }

                if (count($tagreqs) >= $i + 3) {
                    array_push($row, $tagreqs[$i + 2]);
                }

                array_push($tagreqs_final, $row);
            }

            // dd($tagreqs_final);

            return view("newtopic", [
                "eligible" => true,
                "searchbar" => true,
                "requests" => $tagreqs_final
            ]);
        }
    }

    public function request_submit(Request $request)
    {
        /**
         * 1. Check if the topic already exists or not
         * 2. Check if somebody else has requested it or not.
         */

        // Trim tag
        $tag = TagsModel::where('name', trim($request->name))->first();
        if ($tag != null) {
            return Redirect::to(url()->previous())->with([
                'status' => "error",
                'task' => 'request',
                'message' => "Topic Already exists!"
            ]);
        } else {
            $treq = TagRequestsModel::where('name', trim($tag))->first();
            if ($treq != null) {
                return Redirect::to(url()->previous())->with([
                    'status' => "error",
                    'task' => 'request',
                    'message' => "Already requested!"
                ]);
            } else {
                $newtopic = new TagRequestsModel;
                $newtopic->name = trim($request->name);
                $newtopic->msg = "--";
                $newtopic->save();

                return Redirect::to(url()->previous())->with([
                    'status' => "success",
                    'task' => 'request',
                    'message' => "Topic Requested."
                ]);
            }
        }
    }

    public function request_support(Request $request, $req_id)
    {
        /*return Redirect::to(url()->previous())->with([
            'status' => "error",
            'task' => 'support',
            'message' => "Shlob in mi knob"
        ]);*/

        $self = Auth::user();

        $req_obj = TagRequestsModel::where("id", $req_id)->first();

        if ($req_obj == null) {
            return Redirect::to(url()->previous())->with([
                'status' => "error",
                'task' => 'support',
                'message' => "Topic doesn't exist!"
            ]);
        }

        $req_name = $req_obj->name;

        /**
         * $self is tryig to support the request.
         * Add the user to the supporter's list 
         * and increase the 'people' parameter in the table.
         * 
         */

        $people_list = [];
        if (Storage::disk('local')->exists("tag_req/$req_id") == false) {
            Storage::put("tag_req/$req_id", "[]");
        } else {
            $people_list = json_decode(Storage::get("tag_req/$req_id"), true);
        }

        /**
         * Format:
         * [uname1, uname2, ...]
         * 
         */

        if (!in_array($self->username, $people_list)) {
            // Not currently supporting. Support.
            array_push($people_list, $self->username);
            Storage::put("tag_req/$req_id", json_encode($people_list, true));

            $req_obj->people++;
            $req_obj->save();

            return Redirect::to(url()->previous())->with([
                'status' => "success",
                'task' => 'support',
                'message' => "Supported Topic '$req_name'!"
            ]);
        }

        return Redirect::to(url()->previous())->with([
            'status' => "success",
            'task' => 'support',
            'message' => "Already supporting Topic '$req_name'!"
        ]);
    }
}
