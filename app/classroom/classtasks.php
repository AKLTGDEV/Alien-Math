<?php

namespace App\classroom;


use App\CAttModel;
use App\classroom;
use App\ClassroomModel;
use App\docuploadModel;
use App\Http\Controllers\ClassroomController;
use App\NotifsModel;
use Symfony\Component\HttpFoundation\Request;
use Validator;
use Redirect;
use Illuminate\Support\Facades\Input;
use App\tags;
use App\TagsModel;
use App\UserModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class classtasks
{
    public static function join(Request $request)
    {
        /**
         * Check whether the current user is in the pending list.
         * if not, redirect to spectate instead.
         */

        $class = ClassroomModel::where('id', '=', $request->id)->first();
        if ($class === null) {
            return abort(404);
        }

        $author = UserModel::where("id", $class->author)->first();

        foreach (classroom::pendinglist($class->id) as $pending_user) {
            if ($pending_user == Auth::user()->username) {
                /**
                 * The user is indeed present in the pending list.
                 * 
                 * Remove him from the pending list and add him to the member list.
                 */
                classroom::purge($class->id, Auth::user()->username);
                classroom::addmember($class->id, Auth::user()->username);

                /**
                 * Update profile info.
                 */
                $self_classrooms = json_decode(Auth::user()->classrooms, true);
                array_push($self_classrooms, $class->id);
                Auth::user()->classrooms = json_encode($self_classrooms);
                Auth::user()->save();

                return redirect()->route('viewclassroom', [$class->id]);
            }
        }

        return view("classroom.spectate", [
            "class" => $class,
            "author" => $author,
            "searchbar" => false,
        ]);
    }

    public static function join_api(Request $request)
    {
        /**
         * Check whether the current user is in the pending list.
         * if not, redirect to spectate instead.
         */

        $class = ClassroomModel::where('id', '=', $request->id)->first();
        if ($class === null) {
            return [
                "fucked" => true
            ];
        }

        $author = UserModel::where("id", $class->author)->first();

        foreach (classroom::pendinglist($class->id) as $pending_user) {
            if ($pending_user == Auth::user()->username) {
                /**
                 * The user is indeed present in the pending list.
                 * 
                 * Remove him from the pending list and add him to the member list.
                 */
                classroom::purge($class->id, Auth::user()->username);
                classroom::addmember($class->id, Auth::user()->username);

                /**
                 * Update profile info.
                 */
                $self_classrooms = json_decode(Auth::user()->classrooms, true);
                array_push($self_classrooms, $class->id);
                Auth::user()->classrooms = json_encode($self_classrooms);
                Auth::user()->save();

                return [
                    "fucked" => false
                ];
            }
        }

        return [
            "fucked" => true
        ];
    }

    public static function index(Request $request) // View a classroom
    {
        // Check if the concerned classroom even exists or not
        $class = ClassroomModel::where('id', '=', $request->id)->first();
        $dirname = $class->encname;
        if ($class === null) {
            return abort(404);
        }

        $author = UserModel::where("id", $class->author)->first();

        // Check if the user is a member of the class or is invited to
        // Join the class
        foreach (classroom::pendinglist($class->id) as $pending_user) {
            if ($pending_user == Auth::user()->username) {
                /**
                 * 
                 * The user is invited to the Class
                 * 
                 */
                return view("classroom.acceptinvite", [
                    "class" => $class,
                    "author" => $author,
                    "searchbar" => false
                ]);
            }
        }
        // If we have reached here, it means that the user is not in pending list.
        // Check if he is already a member.

        foreach (classroom::memberlist($class->id) as $pending_user) {
            if ($pending_user == Auth::user()->username) {
                /**
                 * 
                 * The user is a member of the Class
                 * 
                 */

                $author = UserModel::where("id", $class->author)->first();

                if ($class->author == Auth::user()->id) {
                    $isadmin = true;
                } else {
                    $isadmin = false;
                }

                // Get the Documents
                $docs = docuploadModel::where("cid", $class->id)->get();
                $docs_final = [];
                foreach ($docs as $doc) {
                    array_push($docs_final, [
                        "title" => $doc->title,
                        "time" => $doc->time,
                        "accepted" => $doc->accepted,
                    ]);
                }

                /**
                 * List the members
                 */
                $members_list = classroom::memberlist($class->id);

                /**
                 * List the worksheets
                 */
                $ws_list = [];

                $actilog_crude = classroom::get_actilog($class->id);
                foreach ($actilog_crude as $crude_item) {
                    $type = $crude_item['type'];
                    $ws_att = false; //For Worksheets

                    if ($type == 3) {
                        //WS
                        $object = json_decode(Storage::get("classrooms/" . $dirname . "/worksheets//" . $crude_item['name']), true);

                        array_push($ws_list, [
                            'encname'   => $crude_item['name'],
                            'title'     => $object['title'],
                        ]);
                    }
                }

                /**
                 * List the Questions
                 */
                $q_list = [];

                foreach ($actilog_crude as $crude_item) {
                    $type = $crude_item['type'];

                    if ($type == 2) {
                        //Question
                        $object = json_decode(Storage::get("classrooms/" . $dirname . "/questions//" . $crude_item['name']), true);
                        array_push($q_list, [
                            'encname'   => $crude_item['name'],
                            'title'     => $object['title'],
                        ]);
                    }
                }

                return view("classroom.view.class", [
                    "class"         => $class,
                    "isadmin"       => $isadmin,
                    "docs"          => $docs_final,
                    "members"       => $members_list,
                    "worksheets"    => $ws_list,
                    "questions"     => $q_list,
                    "pendinglist" => classroom::pendinglist($class->id),
                    "memberlist" => classroom::memberlist($class->id),
                    "searchbar"     => true
                ]);
            }
        }

        // The user is not a member of the class. SPECTATE.
        return view("classroom.spectate", [
            "class" => $class,
            "author" => $author,
            "searchbar" => false
        ]);
    }

    public static function timeline(Request $request)
    {
        // Check if the concerned classroom even exists or not
        $class = ClassroomModel::where('id', '=', $request->id)->first();
        if ($class === null) {
            return abort(404);
        }

        $author = UserModel::where("id", $class->author)->first();

        // Check if the user is a member of the class or is invited to
        // Join the class
        foreach (classroom::pendinglist($class->id) as $pending_user) {
            if ($pending_user == Auth::user()->username) {
                /**
                 * 
                 * The user is invited to the Class
                 * 
                 */
                return view("classroom.acceptinvite", [
                    "class" => $class,
                    "author" => $author,
                    "searchbar" => false
                ]);
            }
        }
        // If we have reached here, it means that the user is not in pending list.
        // Check if he is already a member.

        foreach (classroom::memberlist($class->id) as $pending_user) {
            if ($pending_user == Auth::user()->username) {
                /**
                 * 
                 * The user is a member of the Class
                 * 
                 */

                /**
                 * JUST OPEN THE NEW TIMELINE VIEW, ACTILOG WILL BE RETRIEVED USING AJAX
                 */

                // Get the Documents
                $docs = docuploadModel::where("cid", $class->id)->get();
                $docs_final = [];
                foreach ($docs as $doc) {
                    array_push($docs_final, [
                        "title" => $doc->title,
                        "time" => $doc->time,
                        "accepted" => $doc->accepted,
                    ]);
                }

                if ($class->author == Auth::user()->id) {
                    $isadmin = true;
                } else {
                    $isadmin = false;
                }

                return view("classroom.view.timeline-new", [
                    "class" => $class,
                    "isadmin" => $isadmin,
                    "docs" => $docs_final,
                    "searchbar" => true
                ]);
            }
        }

        // The user is not a member of the class. SPECTATE.
        return view("classroom.spectate", [
            "class" => $class,
            "author" => $author,
            "searchbar" => true
        ]);
    }



    public static function people(Request $request)
    {
        // Check if the concerned classroom even exists or not
        $class = ClassroomModel::where('id', '=', $request->id)->first();
        if ($class === null) {
            return abort(404);
        }

        $author = UserModel::where("id", $class->author)->first();

        // Check if the user is a member of the class or is invited to
        // Join the class
        foreach (classroom::pendinglist($class->id) as $pending_user) {
            if ($pending_user == Auth::user()->username) {
                /**
                 * 
                 * The user is invited to the Class
                 * 
                 */
                return view("classroom.acceptinvite", [
                    "class" => $class,
                    "author" => $author,
                    "searchbar" => false
                ]);
            }
        }
        // If we have reached here, it means that the user is not in pending list.
        // Check if he is already a member.

        foreach (classroom::memberlist($class->id) as $pending_user) {
            if ($pending_user == Auth::user()->username) {
                /**
                 * 
                 * The user is a member of the Class
                 * 
                 */

                $author = UserModel::where("id", $class->author)->first();

                if ($class->author == Auth::user()->id) {
                    $isadmin = true;
                } else {
                    $isadmin = false;
                }

                // Get the Documents
                $docs = docuploadModel::where("cid", $class->id)->get();
                $docs_final = [];
                foreach ($docs as $doc) {
                    array_push($docs_final, [
                        "title" => $doc->title,
                        "time" => $doc->time,
                        "accepted" => $doc->accepted,
                    ]);
                }

                return view("classroom.view.people", [
                    "class" => $class,
                    "isadmin" => $isadmin,
                    "pendinglist" => classroom::pendinglist($class->id),
                    "memberlist" => classroom::memberlist($class->id),
                    "docs" => $docs_final,
                    "searchbar" => true
                ]);
            }
        }

        // The user is not a member of the class. SPECTATE.
        return view("classroom.spectate", [
            "class" => $class,
            "author" => $author,
            "searchbar" => false
        ]);
    }


    public static function stream(Request $request)
    {
        // Check if the concerned classroom even exists or not
        $class = ClassroomModel::where('id', '=', $request->id)->first();
        if ($class === null) {
            return abort(404);
        }

        $author = UserModel::where("id", $class->author)->first();

        // Check if the user is a member of the class or is invited to
        // Join the class
        foreach (classroom::pendinglist($class->id) as $pending_user) {
            if ($pending_user == Auth::user()->username) {
                /**
                 * 
                 * The user is invited to the Class
                 * 
                 */
                return view("classroom.acceptinvite", [
                    "class" => $class,
                    "author" => $author,
                    "searchbar" => false
                ]);
            }
        }
        // If we have reached here, it means that the user is not in pending list.
        // Check if he is already a member.

        foreach (classroom::memberlist($class->id) as $pending_user) {
            if ($pending_user == Auth::user()->username) {
                /**
                 * 
                 * The user is a member of the Class
                 * 
                 */

                $author = UserModel::where("id", $class->author)->first();

                if ($class->author == Auth::user()->id) {
                    $isadmin = true;
                } else {
                    $isadmin = false;
                }

                // Get the Documents
                $docs = docuploadModel::where("cid", $class->id)->get();
                $docs_final = [];
                foreach ($docs as $doc) {
                    array_push($docs_final, [
                        "title" => $doc->title,
                        "time" => $doc->time,
                        "accepted" => $doc->accepted,
                    ]);
                }

                return view("classroom.view.stream", [
                    "class" => $class,
                    "isadmin" => $isadmin,
                    "pendinglist" => classroom::pendinglist($class->id),
                    "memberlist" => classroom::memberlist($class->id),
                    "docs" => $docs_final,
                    "searchbar" => true
                ]);
            }
        }

        // The user is not a member of the class. SPECTATE.
        return view("classroom.spectate", [
            "class" => $class,
            "author" => $author,
            "searchbar" => false
        ]);
    }



    public static function sendinvite(Request $request)
    {
        // Check if the concerned classroom even exists or not
        $class = ClassroomModel::where('id', '=', $request->id)->first();
        if ($class === null) {
            return abort(404);
        }

        $author = UserModel::where("id", $class->author)->first();

        // Check if the user is a member of the class
        foreach (classroom::pendinglist($class->id) as $pending_user) {
            if ($pending_user == Auth::user()->username) {
                /**
                 * 
                 * The user is invited to the Class
                 * 
                 */
                return abort(404);
            }
        }
        // If we have reached here, it means that the user is not in pending list.
        // Check if he is already a member.

        foreach (classroom::memberlist($class->id) as $pending_user) {
            if ($pending_user == Auth::user()->username) {
                /**
                 * 
                 * The user is a member of the Class
                 * 
                 */

                $author = UserModel::where("id", $class->author)->first();
                $invitee = UserModel::where("username", $request->username)->first();

                /**
                 * now, send an invite to the person, if he's not already invited.
                 */

                $memberlist = classroom::memberlist($class->id);
                $pendinglist = classroom::pendinglist($class->id);

                if ($invitee == null) {
                    return Redirect::to(url()->previous())->with([
                        "status" => "error",
                        "message" => "User @" . $request->username . " does not exist",
                    ]);
                }

                if (in_array($invitee->username, $memberlist)) {
                    return Redirect::to(url()->previous())->with([
                        "status" => "error",
                        "message" => $invitee->username . " is already a member",
                    ]);
                }

                if (in_array($invitee->username, $pendinglist)) {

                    return Redirect::to(url()->previous())->with([
                        "status" => "error",
                        "message" => $invitee->username . " is already in the pending list",
                    ]);
                }

                // Invite the user.
                array_push($pendinglist, $invitee->username);
                $contents = json_decode(Storage::get("classrooms/" . $class->encname . "/info.json"));
                $contents->pending_invites = $pendinglist;
                Storage::put("classrooms/" . $class->encname . "/info.json", json_encode($contents));

                $newNotif = new NotifsModel;
                $newNotif->for = $invitee->id;
                $newNotif->type = 4;
                $newNotif->from = Auth::user()->id;
                $newNotif->postid = $class->id;
                $newNotif->seen = 0;
                $newNotif->save();


                return Redirect::to(url()->previous())->with([
                    "status" => "success",
                    "message" => "Sent invite to " . $invitee->username,
                ]);
            }
        }

        // The user is not a member of the class.
        return abort(404);
    }

    public static function sendinvite_api(Request $request)
    {
        // Check if the concerned classroom even exists or not
        $class = ClassroomModel::where('id', '=', $request->id)->first();
        if ($class === null) {
            return abort(404);
        }

        $author = UserModel::where("id", $class->author)->first();

        // Check if the user is a member of the class
        foreach (classroom::pendinglist($class->id) as $pending_user) {
            if ($pending_user == Auth::user()->username) {
                /**
                 * 
                 * The user is invited to the Class
                 * 
                 */
                return abort(404);
            }
        }
        // If we have reached here, it means that the user is not in pending list.
        // Check if he is already a member.

        foreach (classroom::memberlist($class->id) as $pending_user) {
            if ($pending_user == Auth::user()->username) {
                /**
                 * 
                 * The user is a member of the Class
                 * 
                 */

                $author = UserModel::where("id", $class->author)->first();
                $invitee = UserModel::where("username", $request->username)->first();

                /**
                 * now, send an invite to the person, if he's not already invited.
                 */

                $memberlist = classroom::memberlist($class->id);
                $pendinglist = classroom::pendinglist($class->id);

                if ($invitee == null) {
                    return [
                        "fucked" => true,
                        "message" => "User @" . $request->username . " does not exist",
                    ];
                }

                if (in_array($invitee->username, $memberlist)) {
                    return [
                        "fucked" => true,
                        "message" => $invitee->username . " is already a member",
                    ];
                }

                if (in_array($invitee->username, $pendinglist)) {

                    return [
                        "fucked" => true,
                        "message" => $invitee->username . " is already in the pending list",
                    ];
                }

                // Invite the user.
                array_push($pendinglist, $invitee->username);
                $contents = json_decode(Storage::get("classrooms/" . $class->encname . "/info.json"));
                $contents->pending_invites = $pendinglist;
                Storage::put("classrooms/" . $class->encname . "/info.json", json_encode($contents));

                $newNotif = new NotifsModel;
                $newNotif->for = $invitee->id;
                $newNotif->type = 4;
                $newNotif->from = Auth::user()->id;
                $newNotif->postid = $class->id;
                $newNotif->seen = 0;
                $newNotif->save();


                return [
                    "fucked" => false,
                    "message" => "Sent invite to " . $invitee->username,
                ];
            }
        }

        // The user is not a member of the class.
        return abort(404);
    }

    public static function listmembers(Request $request)
    {
        // Check if the concerned classroom even exists or not
        $class = ClassroomModel::where('id', '=', $request->id)->first();
        if ($class === null) {
            return abort(404);
        }

        $author = UserModel::where("id", $class->author)->first();

        // Check if the user is a member of the class
        foreach (classroom::pendinglist($class->id) as $pending_user) {
            if ($pending_user == Auth::user()->username) {
                /**
                 * 
                 * The user is invited to the Class
                 * 
                 */
                return [
                    "fucked" => true,
                    "msg" => "Unknown error"
                ];
            }
        }
        // If we have reached here, it means that the user is not in pending list.
        // Check if he is already a member.

        foreach (classroom::memberlist($class->id) as $pending_user) {
            if ($pending_user == Auth::user()->username) {
                /**
                 * 
                 * The user is a member of the Class
                 * 
                 */

                $memberlist = classroom::memberlist($class->id);

                return [
                    "fucked" => false,
                    "members" => $memberlist
                ];
            }
        }

        // The user is not a member of the class.
        return [
            "fucked" => true,
            "msg" => "Unknown error"
        ];
    }

    public static function new(Request $request) // Create a classroom
    {
        return view("classroom.new", [
            "searchbar" => false,
            "tags_suggested" => tags::top20(),
        ]);
    }

    public static function newsubmit(Request $request)
    {
        /**
         * STEP 1: Create a DB entry.
         */

        $all = $request->all();
        $title = $all['name'];

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
            } else {
                $invites_new = [];
            }
        }

        //$invites_new = explode(",", $all['invites']);

        if ($all['tags'] != null) {
            $tags = explode(",", $all['tags']);
            $tags_new = array();
            foreach ($tags as $tag) {
                $tag = trim($tag);
                $tag_entry = TagsModel::where('name', $tag)->first();
                array_push($tags_new, $tag_entry->name);
            }
            $tags = $tags_new;
        } else {
            $tags = [];
        }


        $classroom = new ClassroomModel;
        $classroom->name = $title;
        $classroom->tags = json_encode($tags);
        $classroom->author = Auth::user()->id;
        $classroom->encname = md5($title . Auth::user()->username . rand(0, 100));
        $classroom->save();


        /**
         * STEP 3: Make a local storage entry
         */
        $dirname = $classroom->encname;
        Storage::makeDirectory("classrooms/" . $dirname);
        Storage::makeDirectory("classrooms/" . $dirname . "/worksheets");
        Storage::makeDirectory("classrooms/" . $dirname . "/questions");
        Storage::makeDirectory("classrooms/" . $dirname . "/notes");
        $info = [
            "name" => $title,
            "pending_invites" => $invites_new,
            "members" => []
        ];
        Storage::put("classrooms/" . $dirname . "/info.json", json_encode($info));
        Storage::put("classrooms/" . $dirname . "/actilog.json", "[]");

        /**
         * STEP 3: Send notifs to invited people
         */

        if ($invites != "[]") {
            //dd($invites_new);
            foreach ($invites_new as $uname) {
                $person = UserModel::where("username", $uname)->first();
                $newNotif = new NotifsModel;
                $newNotif->for = $person->id;
                $newNotif->type = 4;
                $newNotif->from = Auth::user()->id;
                $newNotif->postid = $classroom->id;
                $newNotif->seen = 0;

                $newNotif->save();
            }
        }
        /**
         * STEP 4: Add current user as a member,
         *         Update profile DB entry
         */
        classroom::addmember($classroom->id, Auth::user()->username);
        $self_classrooms = json_decode(Auth::user()->classrooms, true);
        array_push($self_classrooms, $classroom->id);
        Auth::user()->classrooms = json_encode($self_classrooms);
        Auth::user()->save();

        return redirect()->route('viewclassroom', [$classroom->id]);
    }

    public static function validator(Request $request)
    {
        $rules = array(
            //"invites" => ['string', new usersexist], NOT MANDATORY
            //"tags" => ['required', 'string', new tagexists, new tags_min_2], NOT MANDATORY
            "name" => ["string"]
        );

        //$rules['tags'] = ['required', 'string', new tagexists, new tags_min_2];


        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {

            return Redirect::to(url()->previous())
                ->withErrors($validator)
                ->withInput(Input::all());
        } else {
            return classtasks::newsubmit($request);
        }
    }

    public static function delete(Request $request, $cid)
    {
        /**
         * TODO: Add Captcha facility to verify deletion
         * 
         */

        $class = ClassroomModel::where('id', $cid)->first();
        if ($class === null) {
            return abort(404);
        }

        if ($class->author == Auth::user()->id) {
            // Is Admin

            //STEP 1: Delete entry from $user->classrooms array
            //        (for the admin and all of its members)

            foreach (classroom::memberlist($cid) as $mem) {
                // $mem is the username of the user.
                $curr_user = UserModel::where("username", $mem)->first();
                $user_classrooms = json_decode($curr_user->classrooms, true);

                $new_cls_list = [];
                foreach ($user_classrooms as $c) {
                    if ($c != $cid) {
                        array_push($new_cls_list, $c);
                    }
                }

                $curr_user->classrooms = json_encode($new_cls_list, true);
                $curr_user->save();

                /**
                 * TODO Also, remove the classroom notifs for each user later.
                 */
            }

            // STEP 2: Delete the local directory
            Storage::deleteDirectory("classrooms/$class->encname");

            // STEP 3: Delete the table entry
            $class->delete();

            return redirect()->route('namedprofile', Auth::user()->username);
        } else {
            return abort(403);
        }
    }

    public static function remove_user(Request $request, $cid, $username)
    {
        /**
         * TODO: Add Captcha facility to verify deletion
         * 
         */

        $class = ClassroomModel::where('id', $cid)->first();
        if ($class === null) {
            return abort(404);
        }

        if ($class->author != Auth::user()->id) {
            return abort(403);
        }

        $members_list = classroom::memberlist($cid);
        if (!in_array($username, $members_list)) {
            return abort(403);
        }

        if ($username == Auth::user()->username) {
            return Redirect::to(url()->previous())->with([
                "status" => "error",
                "message" => "Can't remove yourself!",
            ]);
        }

        $dirname = $class->encname;
        $contents = json_decode(Storage::get("classrooms/" . $dirname . "/info.json"));

        /**
         * Remove any Classroom attempts from the DB
         */
        $user_atts = CAttModel::where("classid", $cid)
            ->where("attemptee", $username)
            ->get();

        foreach ($user_atts as $att) {
            $att->delete();
        }

        $updated_members_list = [];
        foreach ($members_list as $m) {
            if ($m != $username) {
                array_push($updated_members_list, $m);
            }
        }
        $contents->members = $updated_members_list;
        Storage::put("classrooms/" . $dirname . "/info.json", json_encode($contents));

        $class->users--;
        $class->save();

        return Redirect::to(url()->previous())->with([
            "status" => "success",
            "message" => "User @$username removed from the class",
        ]);
    }

    public static function remove_pendinguser(Request $request, $cid, $username)
    {
        /**
         * TODO: Add Captcha facility to verify deletion
         * 
         */

        $class = ClassroomModel::where('id', $cid)->first();
        if ($class === null) {
            return abort(404);
        }

        if ($class->author != Auth::user()->id) {
            return abort(403);
        }

        $pending_list = classroom::pendinglist($cid);
        if (!in_array($username, $pending_list)) {
            return abort(403);
        }

        if ($username == Auth::user()->username) {
            return abort(500);
        }

        $dirname = $class->encname;
        $contents = json_decode(Storage::get("classrooms/" . $dirname . "/info.json"));

        $updated_pending_list = [];
        foreach ($pending_list as $p) {
            if ($p != $username) {
                array_push($updated_pending_list, $p);
            }
        }
        $contents->pending_invites = $updated_pending_list;
        Storage::put("classrooms/" . $dirname . "/info.json", json_encode($contents));

        return Redirect::to(url()->previous())->with([
            "status" => "success",
            "message" => "Invite sent to User @$username is cancelled",
        ]);
    }
}
