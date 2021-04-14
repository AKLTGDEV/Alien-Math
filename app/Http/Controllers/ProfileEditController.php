<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\UserModel;
use Illuminate\Support\Facades\Storage;
use Validator;
use App\Rules\tagexists;
use App\Rules\tags_min_2;
use App\Rules\biocompact;
use App\Rules\username_unique;
use App\tags;
use App\TagsModel;
use App\users;
use App\WorksheetModel;
use Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator as IlluminateValidator;
use TeamTNT\TNTSearch\TNTSearch;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;

class ProfileEditController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function setup()
    {
        /**
         * Check if the User has grade/level set to null.
         * 
         */

        $user = Auth::user();
        if ($user->grade == null || $user->level == null) {
            $first_time = false;
            if (count(json_decode(users::gettags($user->username), true)) == 0) {
                $first_time = true;
            }

            if (!$first_time) {
                return redirect()->route('home');
            } else {
                //return view("profile.setup", [ // Select Tags
                return view("profile.setup2", [ // Select Grade and Level (New)
                    "user" => $user,
                    "ext" => users::get_ext($user->username),
                    "searchbar" => false,
                    "tags_suggested" => tags::top20(),
                ]);
            }
        } else {
            return redirect()->route('stats');
        }
    }

    public function setup_submit(Request $request)
    {
        Auth::user()->grade = $request->grade;
        Auth::user()->level = $request->level;
        Auth::user()->save();

        return redirect()->back();
    }

    public function view()
    {
        $user = Auth::user();

        $first_time = false;
        if (count(json_decode(users::gettags($user->username), true)) == 0) {
            $first_time = true;
        }

        return view("profile.edit.basic", [
            "user" => $user,
            "ext" => users::get_ext($user->username),
            "searchbar" => true,
            "tags_suggested" => tags::top20(),
            "newuser" => $first_time,
        ]);
    }
    public function submit(Request $request)
    {
        if (
            Session::has('PUBLIC_WSATT_SLUG')
            && Session::has('PUBLIC_WSATT_PID')
        ) {
            /**
             * This boi is coming straight from attempting a Public WS
             * redirect to wsanswer-3
             */

            $ws = WorksheetModel::where("slug", Session::get('PUBLIC_WSATT_SLUG'))->first();
            return redirect()->route("wsanswer-3", [Session::get('PUBLIC_WSATT_SLUG')]);
        }

        $user = Auth::user();
        $image = $request->img;
        $all = $request->all();

        if ($all['bio'] != NULL) {
            users::storebio($user->username, $all['bio']);
        }

        if (isset($all['tags'])) {
            if ($all['tags'] != NULL) {
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
            }
        }

        if ($image == null) {
            return redirect()->route('namedprofile', [$user->username]);
        } else {
            if ($image->storeAs("profilepx/", $user->username, 'local')) {
                return redirect()->route('namedprofile', [$user->username]);
            } else {
                return abort(500);
            }
        }
    }

    public static function validator(Request $request)
    {
        $rules = array(
            //'bio'   => ['required', 'string', new biocompact], Not mandatory
            //'tags'  => ['required', 'string', new tagexists, new tags_min_2],
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            $messages = $validator->messages();
            return Redirect::to(url()->previous())
                ->withErrors($validator)
                ->withInput(Input::all());
        } else {
            $PE = new ProfileEditController();
            return $PE->submit($request);
        }
    }

    public function account()
    {
        $user = Auth::user();

        $first_time = false;
        if (count(json_decode(users::gettags($user->username), true)) == 0) {
            $first_time = true;
        }

        return view("profile.edit.account", [
            "user" => $user,
            "ext" => users::get_ext($user->username),
            "searchbar" => true,
            "newuser" => $first_time,
        ]);
    }

    public static function create_username(Request $request)
    {
        if (Auth::user()->username == null) {
            return view("profile.createusername", [
                "user" => Auth::user(),
                "searchbar" => false,
            ]);
        } else {
            return redirect()->route('namedprofile', [Auth::user()->username]);
        }
    }

    public static function create_username_submit(Request $request)
    {

        if (Auth::user()->username == null) {
            $input = $request->all();
            $validator = Validator::make($input, [
                //'username' => ['required', new username_unique]
                'username' => ['required', 'string', 'max:255', 'unique:users', 'alpha_dash'],
            ]);

            if ($validator->fails()) {
                return Redirect::to(url()->previous())
                    ->withErrors($validator)
                    ->withInput(Input::all());
            } else {
                // The Username is $request->username.
                $u = Auth::user();
                $u->username = $request->username;
                $u->save();

                /**
                 * Create an entry in the `user_ext` folder
                 */

                if (count(explode(" ", $u->name)) < 2) {
                    $fname = $u->name;
                    $lname = null;
                } else {
                    $fname = explode(" ", $u->name)[0];
                    $lname = explode(" ", $u->name)[1];
                }

                $user_ext_data = [
                    "fname" => $fname,
                    "lname" => $lname,
                    "address" => null,
                    "phone" => null,
                    "occupation" => null,
                ];

                Storage::put("user_ext/" . $u->username, json_encode($user_ext_data));
                Storage::put("answers/" . $u->username, "[]");

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
                    'id' => $u->id,
                    'username' => $u->username,
                ]);
            }
        }

        //return redirect()->route('namedprofile', [$request->username]);
        return redirect()->route('home');
    }

    public function subimage(Request $request)
    {
        $user = Auth::user();
        //$image = $request->img;
        $image = Input::get("img");

        $image_parts = explode(";base64,", $image);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        //$file = UPLOAD_DIR . uniqid() . '.png';
        //file_put_contents($file, $image_base64);

        if ($image_base64 == null) {
            return [
                'status' => 'error',
                'message' => 'Failed to Upload Image',
            ];
        } else {
            //if ($image_base64->storeAs("profilepx/", $user->username, 'local')) {
            if (file_put_contents(storage_path("app/profilepx/$user->username"), $image_base64)) {
                return [
                    'status' => 'success',
                    'message' => 'Image Uploaded Successfully',
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Failed to Upload Image',
                ];
            }
        }
    }

    public function connect_insta(Request $request)
    {
        $client_id     = env('INSTAGRAM_CLIENT_ID');
        $client_secret = env('INSTAGRAM_CLIENT_SECRET');
        $redirect      = env('INSTAGRAM_REDIRECT');

        /**
         * TODO FIXME
         * 
         * Implement Connection here
         */
    }
}
