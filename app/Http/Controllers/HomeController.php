<?php

namespace App\Http\Controllers;

use App\groups;
use App\newsfeed;
use App\payments;
use App\PaymentsModel;
use App\sidebar\people;
use App\sidebar\tags;
use App\UserModel;
use App\users;
use App\WorksheetModel;
use App\worksheets;
use Auth;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware([
            'auth',
            //'verified'
        ]);
    }
    public function req_feed(Request $request)
    {
        app('debugbar')->disable();

        //\Debugbar::info('NF TEST');
        //return newsfeed::newsfeed(Auth::user(), $request);

        return newsfeed::nf_call(Auth::user(), $request);
    }
    public function index(Request $request)
    {
        $user = Auth::user();

        /**
         * FIRSTLY, CHECK IF THE USER HAS A USERNAME OR NOT. (THIS
         * CAN HAPPEN IF THE USER SIGNED UP USING SOME SOCIAL MEDIA).
         * IF NOT, REDIRECT HIM TO THE 'CREATE USERNAME' PAGE.
         * 
         */
        if ($user->username == null) {
            return redirect()->route('createusername');
        }

        /**
         * Check if the user has no tags set up. In that case,
         * redirect to the edit profile page.
         */

        if (users::gettags($user->username) == "[]") {
            //return redirect()->route('useredit');
            return redirect()->route('usersetup');
        }

        /**
         * Check user's type. If the user is a "student", redirect to the Student Homepage.
         * 
         */
        if ($user->type == "student") {
            return redirect()->route('student.home');
        }

        $people_data = people::get($user);
        $people_flag = $people_data['flag'];
        $people_list = $people_data['list'];


        $tags_data = tags::get($user);
        $tags_flag = $tags_data['flag'];
        $tags_list = $tags_data['list'];


        return view("home", [
            //"newsfeed" => $newsfeed,
            "user" => $user,
            "carousel" => false,

            "people_to_follow_flag" => $people_flag,
            "people_to_follow" => $people_list,

            "tags_to_follow_flag" => $tags_flag,
            "tags_to_follow" => $tags_list,

            "additional_pins_flag" => false,
            "additional_pins" => null,

            "META_TITLE" => "Home / CrowDoubt",
            "searchbar" => true,
        ]);
    }

    public function explore(Request $request)
    {
        // Get all the 'internal' accounts and the WS posted by them

        $internal_accs = groups::getinternals();

        $int_ws_list = [];
        foreach ($internal_accs as $int_name) {
            $current_user_ws = [];

            $self = UserModel::where("username", $int_name)->first();
            $ws_list = WorksheetModel::where("author", $self->id)->get();

            foreach ($ws_list as $w) {
                $ws = worksheets::get($w->id);
                array_push($current_user_ws, $ws);
            }

            $int_ws_list[$int_name] = $current_user_ws;
        }

        //return $int_ws_list;

        return view("explore", [
            "internals" => $internal_accs,
            "material" => $int_ws_list,
            "searchbar" => true
        ]);
    }

    public function premium_index(Request $request)
    {

        /**
         * Check if the user is already a premium member or not.
         * If there exists no entry in the "payment" table, or 
         * the last entry in the table is 1 year + old, then 
         * the user is not a premium member.
         * 
         */

        //$premium_bool = false;

        /*$payments = PaymentsModel::where("from", Auth::user()->username)
            ->where("created_at", ">", Carbon::now()->subDays(30)->toDateTimeString())
            ->first();

        if ($payments == null) {
            // The user is not a premium Member

            $order = payments::razorpay_order(900);

            return view("premium", [
                "is_premium" => 0,
                "order" => $order,
                "user" => Auth::user(),
                "searchbar" => false
            ]);
        } else {
            // The user is a premium Member
            $exp_date = $payments->created_at->addDays(30)->diffForHumans();

            return view("premium", [
                "is_premium" => 1,
                "order" => null,
                "user" => Auth::user(),
                "exp_date" => $exp_date,
                "searchbar" => false
            ]);
        }*/

        if (Auth::user()->isPremium()) {
            // The user is a premium Member
            return view("premium", [
                "is_premium" => 1,
                "order" => null,
                "user" => Auth::user(),
                "exp_date" => Auth::user()->premium_expdate(),
                "searchbar" => false
            ]);
        } else {
            // The user is not a premium Member
            $order = payments::razorpay_order(150);
            return view("premium", [
                "is_premium" => 0,
                "order" => $order,
                "user" => Auth::user(),
                "searchbar" => false
            ]);
        }
    }
}
