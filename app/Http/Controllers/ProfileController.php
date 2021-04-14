<?php

namespace App\Http\Controllers;

use App\activitylog;
use App\ClassroomModel;
use App\newsfeed;
use Illuminate\Support\Facades\Auth;
use App\posts;
use App\profile\actilog;
use App\profile\classic;
use App\rating;
use App\relations;
use App\tags;
use App\TSModel;
use App\worksheets;
use App\UserModel;
use App\users;
use App\WorksheetModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function view($profile_uname)
    {
        // Check if the concerned user even exists or not
        $user = UserModel::where('username', '=', $profile_uname)->first();
        if ($user === null) {
            // user doesn't exist
            return abort(404);
        }

        //The user exists. Check if it is the current user.
        $currentuser = Auth::user();
        $first_time = false;
        if ($currentuser->username == $profile_uname) {
            $self = true;

            if (
                //count(json_decode(users::gettags($user->username), true)) <= 2 ||
                $user->nos_A <= 2
            ) {
                $first_time = true;
            }
        } else {
            $self = false;
        }

        $following_flag = false;
        if ($self == false) {
            /**
             * Not the current user's profile. Check if the 
             * current user follows this user or not.
             */

            $self_following = json_decode($currentuser->following, true);
            if (in_array($user->id, $self_following)) {
                $following_flag = true;
            } else {
                $following_flag = false;
            }
        }


        //$SITE_PROFILE_TYPE = "ajax"; // Change it here

        $items = null;
        /*$profile_view_file = "profile-new";

        if($SITE_PROFILE_TYPE == "classic"){
            $profile_view_file = "profile";
            $items = classic::get($user);
        }*/

        /**
         * Check if the user has any classrooms to his name
         * FIXME: Extend this to students participating in a classroom
         */

        $classes = array();
        $classroom_idx = json_decode(Auth::user()->classrooms, true);
        foreach ($classroom_idx as $cid) {
            array_push($classes, ClassroomModel::where("id", $cid)->first());
        }

        /**
         * Get all the test series created by the user/bought by the user.
         */
        $TS_list = [];

        $TS_own = TSModel::where("author", Auth::user()->id)->get();
        foreach ($TS_own as $T) {
            array_push($TS_list, $T);
        }

        $TS_bought = json_decode($user->ts_bought, true);
        foreach ($TS_bought as $tsid) {
            $T = TSModel::where("id", $tsid)->first();
            array_push($TS_list, $T);
        }

        if ($currentuser->username == $user->username) {
            //own account
            if ($currentuser->isStudent()) {
                return view("profile.usertype.student", [
                    "user" => $user,
                    "self" => $self,
                    "items" => $items,
                    "notifs" => false, //FIXME
                    "classes_goals" => true,
                    "classrooms_list" => $classes,
                    "following_flag" => $following_flag,
                    //"newuser" => $first_time,
                    "newuser" => false,
                    "TS" => $TS_list,

                    "tags_suggested" => tags::top20(),

                    "searchbar" => true
                ]);
            } else if ($currentuser->isAdmin()){
                return view("profile.usertype.admin", [
                    "user" => $user,
                    "self" => $self,
                    "items" => $items,
                    "notifs" => false, //FIXME
                    "classes_goals" => true,
                    "classrooms_list" => $classes,
                    "following_flag" => $following_flag,
                    //"newuser" => $first_time,
                    "newuser" => false,
                    "TS" => $TS_list,
        
                    "tags_suggested" => tags::top20(),
        
                    "searchbar" => true
                ]);
            } else if($currentuser->isTeacher()){
                return view("profile.usertype.teacher", [
                    "user" => $user,
                    "self" => $self,
                    "items" => $items,
                    "notifs" => false, //FIXME
                    "classes_goals" => true,
                    "classrooms_list" => $classes,
                    "following_flag" => $following_flag,
                    //"newuser" => $first_time,
                    "newuser" => false,
                    "TS" => $TS_list,
        
                    "tags_suggested" => tags::top20(),
        
                    "searchbar" => true
                ]);
            }
        }
    }

    public function getfeed(Request $request, $uname)
    {
        app('debugbar')->disable();

        $user = UserModel::where("username", $uname)->first();
        return actilog::get($user, $request);
    }

    public function follow($uname)
    {
        $self = Auth::user();
        relations::follow($self->username, $uname);
        rating::update($self->username);

        return Redirect::to(url()->previous());
    }

    public function follow_api($uname)
    {
        $self = Auth::user();
        relations::follow($self->username, $uname);
        rating::update($self->username);

        return [
            "fucked" => false,
            "msg" => "following user @$uname"
        ];
    }


    public function unfollow($uname)
    {
        $self = Auth::user();
        relations::unfollow($self->username, $uname);
        rating::update($self->username);

        return Redirect::to(url()->previous());
    }

    public function unfollow_api($uname)
    {
        $self = Auth::user();
        relations::unfollow($self->username, $uname);
        rating::update($self->username);

        return [
            "fucked" => false,
            "msg" => "unfollowed user @$uname"
        ];
    }

    public function qbank_index(Request $request)
    {
        return view("profile.qbank", [
            "user" => Auth::user(),
            "searchbar" => true
        ]);
    }
}
