<?php

namespace App;

use App\Http\Controllers\WorksheetController;
use App\WorksheetModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use App\wsAttemptsModel;
use App\UserModel;
use App\rating;
use App\Rules\tagexists;
use App\Rules\tags_min_2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use TeamTNT\TNTSearch\TNTSearch;
use App\activitylog;
use App\NotifsModel;
use App\Rules\usersexist;
use App\TagsModel;
use App\tags;
use App\utils\randstring;
use Carbon\Carbon;

class worksheets
{
    public static function exists($id)
    {
        $ws = WorksheetModel::where('id', $id)->first();
        if ($ws != null) {
            return true;
        } else {
            return false;
        }
    }

    public static function attempted($user, $worksheet)
    {
        // Check if there's an entry on ws_attempts
        $wsid = $worksheet->id;
        $uid = $user->id;

        if (wsAttemptsModel::where('wsid', $wsid)->where('attemptee', $uid)->first() == null) {
            return false;
        } else {
            return true;
        }
    }

    public static function getbody($digest)
    {
        return Storage::get('worksheets/' . $digest);
    }

    public static function get_digests($ws_body)
    {
        return Storage::get('ws_body/' . $ws_body);
    }

    public static function get_opts($ws_body)
    {
        return json_decode(Storage::get('ws_opts/' . $ws_body));
    }

    public static function getwsid_by_title($title)
    {
        $ws = WorksheetModel::where('author', Auth::user()->id)->where('title', $title)->first();
        if ($ws != NULL) {
            return $ws->id;
        } else {
            return 0;
        }
    }

    public static function get($id)
    {
        $ws = WorksheetModel::where('id', $id)->first();
        $user = UserModel::where('id', $ws->author)->first();
        $app_url = Config::get('app.url');

        $attemptees = json_decode($ws->attemptees, true);
        if (in_array($user->id, $attemptees)) {
            $attempted = true;
        } else {
            $attempted = false;
        }

        $own = false;
        if (Auth::check()) {
            if ($ws->author == Auth::user()->id) {
                $own = true;
            }
        }

        $attempts = count(wsAttemptsModel::where("wsid", $ws->id)->get());

        return array(
            'itemT' => 'ws',
            'id' => $ws->id,
            'slug' => $ws->slug,
            'encname' => $ws->ws_name,
            'title' => $ws->title,
            //'attempts' => $ws->attempts,
            'attempts' => $attempts,
            'nos' => $ws->nos,
            'mins' => $ws->mins,
            'name' => $user->name,
            'username' => $user->username,
            'tags' => $ws->tags,
            'profilepic' => "{$app_url}/user/{$user->username}/profilepic",
            'attempted' => $attempted,
            'own'       => $own,
        );
    }

    public static function list($uname)
    {
        $user = UserModel::where('username', $uname)->first();
        $worksheets = WorksheetModel::where('author', $user->id)->get();

        $ws_return_list = [];
        if ($worksheets->isEmpty()) {
            return null;
        } else {
            foreach ($worksheets as $ws) {

                $current_ws = array(
                    'itemT' => 'ws',
                    'id' => $ws->id,
                    'title' => $ws->title,
                    'attempts' => $ws->attempts,
                    'nos' => $ws->nos,
                    'mins' => $ws->mins,
                    'name' => $user->name,
                    'username' => $user->username,
                    'tags' => $ws->tags
                );

                array_push($ws_return_list, $current_ws);
            }

            return $ws_return_list;
        }
    }

    public static function answer_submit_seed($recvd, $self)
    {
        $attempt = new wsAttemptsModel;
        $attempt->answers = $recvd['ans'];
        $worksheet = WorksheetModel::where('id', $recvd['wsid'])->first();
        $author = UserModel::where("id", $worksheet->author)->first();
        $Ttaken = $recvd['Ttaken'];


        $attempt->secs = $Ttaken;
        $worksheet->attempts++;
        $attemptees = json_decode($worksheet->attemptees, true);
        array_push($attemptees, $self->id);
        $worksheet->attemptees = json_encode($attemptees);

        $attempt->wsid = $recvd['wsid'];
        $attempt->attemptee = $self->id;

        $metrics = array();
        array_push($metrics, json_decode($recvd['clock_hits']));
        array_push($metrics, json_decode($recvd['opt_changes']));
        array_push($metrics, []); //Times flicked

        //$attempt->metrics = json_encode($metrics);
        /**
         * Instead of saving the WS metrics on the DB, save 
         * it on local storage, and retrieve it from there.
         */
        //$attempt->metrics = "[]";
        $att_id = $attempt->wsid . "." . $attempt->attemptee;
        Storage::put("wsa_metrics/" . $att_id, json_encode($metrics, true));

        $ws_att = json_decode($self->ws_attempted, true);
        array_push($ws_att, $worksheet->id);
        $self->ws_attempted = json_encode($ws_att);

        $attempt->save();
        $worksheet->save();
        $self->save();

        rating::update($self->username);
        rating::update($author->username);

        return "Y";
    }




    // Main routines
    public static function validator(Request $request)
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
            /*$WSC = new WorksheetController;
            return $WSC->submit($request);*/

            return worksheets::submit($request->all(), Auth::user()->id);
        }
    }

    public static function submit($all, $poster_id)
    {
        $nos = $all['nos'];
        $title = $all['title'];
        $poster_uname = UserModel::where("id", $poster_id)->first()->username;

        $invites = "[]";
        if (array_key_exists("invites", $all)) {
            if ($all['invites'] != null) {
                $invites = explode(",", $all['invites']);
                $invites_new = array();
                foreach ($invites as $inv_u) {
                    $inv_u = trim($inv_u);
                    $inv_u = UserModel::where('username', $inv_u)->first();
                    array_push($invites_new, $inv_u->username);
                }

                $invites = json_encode($invites_new, true);
            }
        }

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


        /**
         * Fix tags.
         */
        $tags = explode(",", $all['tags']);
        $tags_new = array();
        foreach ($tags as $tag) {
            $tag = trim($tag);
            $tag_entry = TagsModel::where('name', $tag)->first();
            array_push($tags_new, $tag_entry->name);
        }
        $tags = $tags_new;

        /**
         * We now have all the essential info we need.
         */

        //$ws_name_ident = md5(rand(0, 69) . $poster_uname . $title . Carbon::now()->toDateTimeString() . rand(0, 69));

        $ws_name_ident = randstring::generate(25);
        while (WorksheetModel::where("ws_name", $ws_name_ident)->first() != null) {
            $ws_name_ident = randstring::generate(25);
        }

        $wsitem_contents = [
            "datetime" => Carbon::now()->toDateTimeString(),
            "title" => $title,
            "author" => $poster_uname,
            "nos" => $nos,
            "bodies" => $qbodies,
            "opts" => $options,
            "correct" => $correct,
            "time" => $all['time'],
            "tags" => $tags,
        ];

        Storage::put("WS/$ws_name_ident", json_encode($wsitem_contents));

        $worksheet = new WorksheetModel;
        $worksheet->title = $title;
        $worksheet->nos = $nos;
        $worksheet->ws_name = $ws_name_ident;
        $worksheet->author = $poster_id;
        $worksheet->tags = json_encode($tags);
        $worksheet->invited = $invites;
        $worksheet->mins = $all['time'];

        $worksheet->save();

        $pretext = $title . " Worksheet by " . UserModel::where("id", $poster_id)->first()->username . " $worksheet->id";
        $worksheet->slug = str_slug($pretext);
        $worksheet->save();

        $self = Auth::user();
        $ws_posted = json_decode($self->ws_posted);
        array_push($ws_posted, $worksheet->id);
        $self->ws_posted = json_encode($ws_posted);
        $self->save();

        /**
         * Generate notification for followers.
         */

        if ($self->followers != "[]") {
            $followers_id_list = json_decode($self->followers);
            foreach ($followers_id_list as $fid) {
                $newNotif = new NotifsModel;
                $newNotif->for = $fid;
                $newNotif->type = 2;
                $newNotif->from = $self->id;
                $newNotif->postid = $worksheet->id;
                $newNotif->seen = 0;

                $newNotif->save();
            }
        }

        /**
         * Generate notification for invitees.
         */
        if ($invites != "[]") {
            foreach (json_decode($invites) as $_inv_) {
                $invNotif = new NotifsModel;
                $i_tee = UserModel::where('username', $_inv_)->first();
                $invNotif->for = $i_tee->id;
                $invNotif->type = 3;
                $invNotif->from = $self->id;
                $invNotif->postid = $worksheet->id;
                $invNotif->seen = 0;

                $invNotif->save();
            }
        }

        $tnt = new TNTSearch;
        $tnt->loadConfig([
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', '127.0.0.1'),
            'database'  => env('DB_DATABASE', 'forge'),
            'username'  => env('DB_USERNAME', 'forge'),
            'password'  => env('DB_PASSWORD', ''),
            'storage'   => storage_path('app') . "/indices//",
        ]);
        $tnt->selectIndex("ws.index");
        $index = $tnt->getIndex();

        $index->insert([
            'id' => $worksheet->id,
            'title' => $worksheet->title
        ]);

        activitylog::post_ws($poster_uname, $worksheet->id);
        rating::update($poster_uname);

        return redirect()->route('home');
    }
}
