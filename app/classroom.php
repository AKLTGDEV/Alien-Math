<?php

namespace App;

use App\ClassroomModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\TagsModel;

class classroom
{
    public static function get_info($id)
    {
        $class = ClassroomModel::where("id", $id)->first();
        $dirname = $class->encname;
        $info = json_decode(Storage::get("classrooms/" . $dirname . "/info.json"), true);
        return $info;
    }

    public static function get_actilog($id)
    {
        $class = ClassroomModel::where("id", $id)->first();
        $dirname = $class->encname;
        $actilog = json_decode(Storage::get("classrooms/" . $dirname . "/actilog.json"), true);
        return $actilog;
    }

    public static function memberlist($id)
    {
        $class = ClassroomModel::where("id", $id)->first();
        $dirname = $class->encname;
        $contents = json_decode(Storage::get("classrooms/" . $dirname . "/info.json"), true);
        return $contents['members'];
    }

    public static function pendinglist($id)
    {
        $class = ClassroomModel::where("id", $id)->first();
        $dirname = $class->encname;
        $contents = json_decode(Storage::get("classrooms/" . $dirname . "/info.json"));
        //return json_decode($contents->pending_invites, true);
        return $contents->pending_invites;
    }

    public static function addmember($cid, $uname)
    {
        $class = ClassroomModel::where("id", $cid)->first();
        $dirname = $class->encname;
        $contents = json_decode(Storage::get("classrooms/" . $dirname . "/info.json"));

        $updated_members_list = $contents->members;
        array_push($updated_members_list, $uname);
        $contents->members = $updated_members_list;
        Storage::put("classrooms/" . $dirname . "/info.json", json_encode($contents));

        $class->users++;
        $class->save();
    }

    public static function purge($cid, $uname)
    {
        /**
         * This removes an user from the pending list.
         */

        $class = ClassroomModel::where("id", $cid)->first();
        $dirname = $class->encname;
        $contents = json_decode(Storage::get("classrooms/" . $dirname . "/info.json"));

        $updated_pending_list = array();

        $pending_list = $contents->pending_invites;
        foreach ($pending_list as $person) {
            if ($person == $uname) {
                // Don't do shitt
            } else {
                array_push($updated_pending_list, $person);
            }
        }

        $contents->pending_invites = $updated_pending_list;
        Storage::put("classrooms/" . $dirname . "/info.json", json_encode($contents));
    }

    public static function postitem_note($cid, $uname, $notebody)
    {
        $class = ClassroomModel::where("id", $cid)->first();
        $dirname = $class->encname;

        $notename = md5($notebody . rand(0, 50) . rand(0, 50) . rand(0, 50));
        $noteitem_contents = [
            "datetime" => Carbon::now()->toDateTimeString(),
            "body" => $notebody,
            "author" => $uname,
        ];
        Storage::put("classrooms/" . $dirname . "/notes//" . $notename, json_encode($noteitem_contents));

        // UPDATE THE ACTIVITY LOG
        $actilog_items = json_decode(Storage::get("classrooms/" . $dirname . "/actilog.json"));
        array_push($actilog_items, [
            "datetime" => Carbon::now()->toDateTimeString(),
            "type" => 1,  //TYPE 1 => NOTE
            "name" => $notename
        ]);

        Storage::put("classrooms/" . $dirname . "/actilog.json", json_encode($actilog_items));
    }

    public static function postitem_question($cid, $uname, $request)
    {
        /**
         * Ensure that only the admin can post questions
         */

        $poster = UserModel::where("username", $uname)->first();
        if ($poster == null) {
            abort(403);
        }

        $class = ClassroomModel::where("id", $cid)->first();
        $dirname = $class->encname;
        $all = $request->all();

        if ($poster->id != $class->author) {
            return false;
        } else {
            $qbody = $all['Qbody'];
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

            /*if ($all['correct'] == '1') {
                $correct_opt = 1;
            } elseif ($all['correct'] == '2') {
                $correct_opt = 2;
            }*/
            $correct_opt = $all['correct'];

            $q_name = md5($qbody . rand(0, 50) . rand(0, 50) . rand(0, 50));

            if (array_key_exists("title", $all)) {
                $qtitle = $all['title'];
            } else {
                $qtitle = null;
            }

            $qitem_contents = [
                "datetime" => Carbon::now()->toDateTimeString(),
                "title" => $qtitle,
                "body" => $qbody,
                "author" => $uname,
                "opts" => $options,
                "correct" => $correct_opt,
                "tags" => $tags,
            ];

            Storage::put("classrooms/" . $dirname . "/questions//" . $q_name, json_encode($qitem_contents));

            // UPDATE THE ACTIVITY LOG
            $actilog_items = json_decode(Storage::get("classrooms/" . $dirname . "/actilog.json"));
            array_push($actilog_items, [
                "datetime" => Carbon::now()->toDateTimeString(),
                "type" => 2,  //TYPE 2 => QUESTION
                "name" => $q_name
            ]);

            Storage::put("classrooms/" . $dirname . "/actilog.json", json_encode($actilog_items));

            return true;
        }
    }

    public static function postitem_ws($cid, $uname, $all)
    {
        /**
         * Ensure that only the admin can post worksheets
         */

        $poster = UserModel::where("username", $uname)->first();
        if ($poster == null) {
            abort(403);
        }

        $class = ClassroomModel::where("id", $cid)->first();
        $dirname = $class->encname;
        //$all = $request->all();

        if ($poster->id != $class->author) {
            return false;
        } else {
            $title = $all['title'];
            $nos = $all['nos'];
            $ws_name = md5($title . rand(0, 50) . rand(0, 50) . rand(0, 50));

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

            $wsitem_contents = [
                "datetime" => Carbon::now()->toDateTimeString(),
                "title" => $title,
                "author" => $uname,
                "nos" => $nos,
                "bodies" => $qbodies,
                "opts" => $options,
                "correct" => $correct,
                "time" => $all['time'],
                "tags" => $tags,
            ];

            Storage::put("classrooms/" . $dirname . "/worksheets//" . $ws_name, json_encode($wsitem_contents));

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
                $wslist[$ws_name] = $qlist;
                $coll->wslist = json_encode($wslist, true);

                $coll->save();
            }

            // UPDATE THE ACTIVITY LOG
            $actilog_items = json_decode(Storage::get("classrooms/" . $dirname . "/actilog.json"));
            array_push($actilog_items, [
                "datetime" => Carbon::now()->toDateTimeString(),
                "type" => 3,  //TYPE 3 => WORKSHEET
                "name" => $ws_name
            ]);

            Storage::put("classrooms/" . $dirname . "/actilog.json", json_encode($actilog_items));

            return true;
        }
    }
}
