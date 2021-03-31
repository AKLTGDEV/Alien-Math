<?php

namespace App\classroom;


use App\CAttModel;
use App\classCollectionModel;
use App\classroom;
use App\ClassroomModel;
use App\docuploadModel;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class other
{
    public static function wsedit(Request $request, $cid)
    {
        // Display the edit WS page with all the details of the WS
        $class = ClassroomModel::where("id", $cid)->first();
        $wsname = $request->wsname;
        $dirname = $class->encname;

        $isadmin = false;
        if ($class->author == Auth::user()->id) {
            $isadmin = true;
        }

        $wsinfo = json_decode(Storage::get("classrooms/" . $dirname . "/worksheets//" . trim($wsname)));

        //return ($wsinfo);

        $nos = $wsinfo->nos;
        $ws_opts = [];
        for ($i = 1; $i <= $nos; $i++) {
            $opts_current = $wsinfo->opts[$i - 1];

            $ws_opts["option1-$i"] = $opts_current[0];
            $ws_opts["option2-$i"] = $opts_current[1];
            $ws_opts["option3-$i"] = $opts_current[2];
            $ws_opts["option4-$i"] = $opts_current[3];
        }

        /**
         * Get the collections of each question
         */
        $class_colls = classCollectionModel::where("classid", $cid)->get();
        $coll_list = [];
        foreach ($class_colls as $coll) {
            $wslist = json_decode($coll->wslist, true);
            foreach ($wslist as $ws => $qlist) {
                if ($ws == $wsname) {
                    // Yep, the current WS.
                    foreach ($qlist as $q) {
                        if (array_key_exists($coll->encname, $coll_list)) {
                            array_push($coll_list[$coll->encname], $q);
                        } else {
                            $coll_list[$coll->encname] = [];
                            array_push($coll_list[$coll->encname], $q);
                        }
                    }
                }
            }
        }

        return view("classroom.ws.edit", [
            "cid" => $cid,
            "class" => $class,
            "isadmin" => $isadmin,
            "nos" => $wsinfo->nos,
            "title" => $wsinfo->title,
            "wsname" => trim($wsname),
            "bodies" => $wsinfo->bodies,
            "options" => $ws_opts,
            "correct" => $wsinfo->correct,
            "time" => $wsinfo->time,
            "collections" => collections::list($class->id),
            "coll_list" => $coll_list,
            "searchbar" => true,
        ]);
    }

    public static function wsremove(Request $request, $cid)
    {
        $class = ClassroomModel::where("id", $cid)->first();
        $wsname = $request->wsname;
        $dirname = $class->encname;

        $isadmin = false;
        if ($class->author == Auth::user()->id) {
            $isadmin = true;
        }

        if (!$isadmin) {
            return abort(403);
        }

        /**
         * 
         * Remove Collection data, Remove WS attempts, remove from actilog,
         * and finally remove the file itself.
         * 
         */

        // FIXME TODO:: Collection
        $colls = classCollectionModel::where("classid", $cid)->get();
        foreach ($colls as $collection) {
            $coll_wslist = json_decode($collection->wslist, true);
            $wslist = array_keys($coll_wslist);

            if (in_array($wsname, $wslist)) {
                // This WS contains the current collection.
                $new_wslist = [];

                foreach ($wslist as $w) {
                    if ($w != $wsname) {
                        $new_wslist[$w] = $coll_wslist[$w];
                    }
                }

                $collection->wslist = json_encode($new_wslist);
                $collection->save();
            }
        }

        $ws_atts = CAttModel::where("classid", $cid)
            ->where("name", $wsname)
            ->get();

        foreach ($ws_atts as $att) {
            $att->delete();
        }

        $actilog = classroom::get_actilog($cid);
        $actilog_new = [];
        foreach ($actilog as $a_item) {
            if ($a_item['name'] != $wsname) {
                array_push($actilog_new, $a_item);
            }
        }
        Storage::put("classrooms/" . $dirname . "/actilog.json", json_encode($actilog_new));

        Storage::delete("classrooms/$dirname/worksheets/$wsname");

        return redirect()->back();
    }

    public static function qedit(Request $request, $cid)
    {
        // TODO
    }

    public static function qremove(Request $request, $cid)
    {
        $class = ClassroomModel::where("id", $cid)->first();
        $qname = $request->qname;
        $dirname = $class->encname;

        $isadmin = false;
        if ($class->author == Auth::user()->id) {
            $isadmin = true;
        }

        if (!$isadmin) {
            return abort(403);
        }

        /**
         * 
         * Remove attempts, remove from actilog, and finally remove the file itself.
         * 
         */

        $q_atts = CAttModel::where("classid", $cid)
            ->where("name", $qname)
            ->get();

        foreach ($q_atts as $att) {
            $att->delete();
        }

        $actilog = classroom::get_actilog($cid);
        $actilog_new = [];
        foreach ($actilog as $a_item) {
            if ($a_item['name'] != $qname) {
                array_push($actilog_new, $a_item);
            }
        }
        Storage::put("classrooms/" . $dirname . "/actilog.json", json_encode($actilog_new));

        Storage::delete("classrooms/$dirname/worksheets/$qname");

        return redirect()->back();
    }

    public static function wseditsubmit(Request $request, $cid, $wsname)
    {
        $all = $request->all();

        /**
         * Step 1: Do some basic error checking
         * Step 2: Gather the already available data (like title, tags, etc)
         * Step 3: Combine the current data with the old data and replace the WSinfo object
         */

        // Error Checking :: FIXME

        $class = ClassroomModel::where("id", $cid)->first();
        $dirname = $class->encname;
        $wsinfo = json_decode(Storage::get("classrooms/" . $dirname . "/worksheets//" . trim($wsname)));
        $nos = $wsinfo->nos;

        $qbodies = array();
        for ($i = 1; $i <= $nos; $i++) {
            array_push($qbodies, $all['Qbody-' . $i]);
        }

        $options = array();
        for ($i = 1; $i <= $nos; $i++) {
            $o_1 = $all['option1-' . $i];
            $o_2 = $all['option2-' . $i];
            $o_3 = $all['option3-' . $i];
            $o_4 = $all['option4-' . $i];
            $O_set = [$o_1, $o_2, $o_3, $o_4];

            array_push($options, $O_set);
        }

        $correct = array();
        for ($i = 1; $i <= $nos; $i++) {
            array_push($correct, $all['correct-' . $i]);
        }

        $wsitem_contents = [
            // Old data
            "datetime" => $wsinfo->datetime,
            "title" => $wsinfo->title,
            "author" => $wsinfo->author,
            "nos" => $wsinfo->nos,
            "tags" => $wsinfo->tags,

            "bodies" => $qbodies,
            "opts" => $options,
            "correct" => $correct,
            "time" => $all['time'],
        ];

        /**
         * Update the collection info if present
         */

        $collection_ref = [];
        foreach (array_keys($all) as $item) {
            if ($all[$item] != "on") {
                continue;
            }
            $possible = explode("---", $item);
            //$possible = split("---", $item);
            if (count($possible) == 3) {
                //Its a collection reference.
                $q_no = $possible[1];
                $coll_name = $possible[2];

                if (array_key_exists($coll_name, $collection_ref)) {
                    array_push($collection_ref[$coll_name], $q_no);
                } else {
                    $collection_ref[$coll_name] = [];
                    array_push($collection_ref[$coll_name], $q_no);
                }
            }
        }

        // $collection_ref now contains the collection info.
        foreach ($collection_ref as $coll_enc => $qlist) {
            $coll = classCollectionModel::where("encname", $coll_enc)
                ->where("classid", $cid)
                ->first();
            //$qlist = $collection_ref[$coll_enc];

            $wslist = json_decode($coll->wslist, true);
            $wslist[$wsname] = $qlist;
            $coll->wslist = json_encode($wslist, true);

            $coll->save();
        }

        Storage::put("classrooms/" . $dirname . "/worksheets//" . $wsname, json_encode($wsitem_contents));
        return redirect()->route('viewclassroom', [$cid]);
    }

    public static function docupload(Request $request, $cid)
    {
        $doc = $request->doc;

        // FIXME BASIC ERROR CHECKING HERE

        $upload = new docuploadModel;
        $upload->cid = $cid;
        $upload->title = $request->title;
        $upload->notes = $request->notes;
        $upload->time = $request->time;
        $upload->poster = Auth::user()->username;
        $upload->original_name = $doc->getClientOriginalName();
        $upload->enc_name = md5($doc->getClientOriginalName() . rand(0, 100) . Auth::user()->username);


        if ($doc == null) {
            return redirect()->back();
        } else {
            if ($doc->storeAs("docs/", $upload->enc_name, 'local')) {
                $upload->save();
                return redirect()->back();
            } else {
                return abort(500);
            }
        }
    }


    public static function jsonupload(Request $request)
    {
        /**
         * User has posted a WS JSON.
         */

        $class = ClassroomModel::where('id', '=', $request->id)->first();
        if ($class === null) {
            return abort(404);
        }

        $all = json_decode(file_get_contents(Input::file('data_json')->getRealPath()), true);

        classroom::postitem_ws($request->id, Auth::user()->username, $all);
        return redirect()->route('viewclassroom', [$class->id]);
    }

    public static function ws_getjson(Request $request, $id)
    {
        $wsname = $request->wsname;

        $class = ClassroomModel::where("id", $id)->first();
        $dirname = $class->encname;

        $raw = Storage::get("classrooms/$dirname/worksheets/$wsname");
        $old_object = json_decode($raw);
        $new_object = [];

        $c = 1;
        foreach ($old_object->bodies as $b) {
            $new_object["Qbody-$c"] = $b;
            $c++;
        }

        $old_opts = $old_object->opts;
        for ($i = 1; $i <= $old_object->nos; $i++) {
            $current_opt = $old_opts[$i - 1];

            $new_object["option1-$i"] = $current_opt[0];
            $new_object["option2-$i"] = $current_opt[1];
            $new_object["option3-$i"] = $current_opt[2];
            $new_object["option4-$i"] = $current_opt[3];
        }

        $old_correct = $old_object->correct;
        for ($i = 1; $i <= $old_object->nos; $i++) {
            $new_object["correct-$i"] = $old_correct[$i - 1];
        }

        $new_object['title']  = $old_object->title;
        $new_object['nos']  = $old_object->nos;
        $new_object['time']  = $old_object->time;
        $new_object['tags']  = implode(',', $old_object->tags);

        $fileName = "$old_object->title.json";
        $headers = [
            'Content-type' => 'text/plain',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName),
            'Content-Length' => strlen(json_encode($new_object))
        ];
        return Response::make(json_encode($new_object), 200, $headers);
    }

    public static function rename(Request $request, $cid)
    {
        $class = ClassroomModel::where("id", $cid)->first();
        $name = $request->name;
        $isadmin = false;
        if ($class->author == Auth::user()->id) {
            $isadmin = true;
        }

        if ($isadmin) {
            $class->name = $name;
            $class->save();

            return Redirect::to(url()->previous())->with([
                'rename-status' => "success",
                'message' => "Name changed",
            ]);
        } else {
            return Redirect::to(url()->previous())->with([
                'rename-status' => "danger",
                'message' => "Access Violation Error",
            ]);
        }

        return redirect()->back();
    }

    public static function get_timeline(Request $request, $cid)
    {
        // Return Timeline items

        $app_url = Config::get('app.url');
        $idx = json_decode($request->idx, true);

        $class = ClassroomModel::where('id', '=', $request->id)->first();
        if ($class === null) {
            return abort(404);
        }

        $TIMELINE_BATCH_LEN = 5;

        $class_info = classroom::get_info($class->id);
        $actilog_crude = classroom::get_actilog($class->id);
        $actilog_crude = array_reverse($actilog_crude);
        $dirname = $class->encname;

        $actilog_final = array();

        //foreach ($actilog_crude as $crude_item) {
        $i = 1;
        while (count($actilog_final) < $TIMELINE_BATCH_LEN) {

            //Check that we are in bound
            if ($i > count($actilog_crude)) {
                break;
            }

            $crude_item = $actilog_crude[$i - 1];
            $type = $crude_item['type'];

            $att_given = null; //For posts
            $ws_att = false; //For Worksheets

            if (other::timeline_item_seen($idx, $type, $crude_item['name'])) {
                $i++;
                continue;
            }

            // If we are here, the item has not been seen by the user
            if ($type == "1") { //NOTE ITEM
                $object = json_decode(Storage::get("classrooms/" . $dirname . "/notes//" . $crude_item['name']), true);
                $author = UserModel::where('username', $object['author'])->first();

                // Add item to feed
                array_push($actilog_final, [
                    'itemT' => 'note',
                    'name' => $author->name,
                    'encname' => $crude_item['name'],
                    'content' => $object,
                    'username' => $author->username,
                    'profilepic' => "{$app_url}/user/{$author->username}/profilepic",
                    "samay" => Carbon::parse($crude_item['datetime'])->diffForHumans(),
                ]);
            }

            if ($type == "2") { //QUESTION ITEM
                //GET Question
                $object = json_decode(Storage::get("classrooms/" . $dirname . "/questions//" . $crude_item['name']), true);
                $author = UserModel::where('username', $object['author'])->first();
                /**
                 * Check if the user has attempted the Question
                 */

                $prevattempt = CAttModel::where("classid", $class->id)
                    ->where("name", $crude_item['name'])
                    ->where("attemptee", Auth::user()->username)
                    ->first();
                if ($prevattempt != null) {
                    $att_given = $prevattempt->body;
                } else {
                    $att_given = null;
                    /**
                     *  Since the user has not attempted the question, remove the "correct opt"
                     *  From the content object. (Security)
                     */
                    $object['correct'] = null;
                }

                $object_body = $object['body'];

                $img_present = false;
                if (strpos($object_body, '<img style=') !== false) {
                    $img_present = true;
                }

                // Normalize the images
                $body_new = str_replace('<img style=', '<img id="postimg-' . $crude_item['name'] . '" class="img-fluid" style=', $object_body);

                $object['body'] = $body_new;

                // Add item to feed
                array_push($actilog_final, [
                    'itemT' => 'post',
                    'name' => $author->name,
                    'img_present' => $img_present,
                    'encname' => $crude_item['name'],
                    'attgiven' => $att_given,
                    'content' => $object,
                    'opts_nos' => count(json_decode($object['opts'], true)),
                    'username' => $author->username,
                    'profilepic' => "{$app_url}/user/{$author->username}/profilepic",
                    'tags' => json_encode($object['tags'], true),
                    'title' => $object['title'],
                    "samay" => Carbon::parse($crude_item['datetime'])->diffForHumans(),
                ]);
            }

            if ($type == "3") { //WS ITEM
                //GET WS
                $object = json_decode(Storage::get("classrooms/" . $dirname . "/worksheets//" . $crude_item['name']), true);
                $author = UserModel::where('username', $object['author'])->first();

                /**
                 * Check if the user has attempted the Worksheet
                 */

                $prevattempt = CAttModel::where("classid", $class->id)
                    ->where("name", $crude_item['name'])
                    ->where("attemptee", Auth::user()->username)
                    ->first();
                if ($prevattempt != null) {
                    $ws_att = true;
                } else {
                    $ws_att = false;
                }

                // Add item to feed
                array_push($actilog_final, [
                    'itemT' => 'ws',
                    'content' => $object,
                    'encname' => $crude_item['name'],
                    'title' => $object['title'],
                    'name' => $author->name,
                    'username' => $author->username,
                    'tags' => json_encode($object['tags'], true),
                    'profilepic' => "{$app_url}/user/{$author->username}/profilepic",
                    'attempted' => $ws_att,
                    "samay" => Carbon::parse($crude_item['datetime'])->diffForHumans(),
                ]);
            }

            //Add to the IDX list
            array_push($idx, [
                "type" => $type,
                "name" => $crude_item['name'],
            ]);
            $i++;
        }

        return [
            "result" => $actilog_final,
            "idx" => $idx,
        ];
    }

    public static function timeline_item_seen($idx_list, $itemtype, $itemname)
    {
        foreach ($idx_list as $idx_item) {
            if ($idx_item['type'] == $itemtype && $idx_item['name'] == $itemname) {
                return true;
            }
        }

        return false;
    }
}
