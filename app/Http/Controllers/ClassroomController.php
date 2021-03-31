<?php

namespace App\Http\Controllers;

use App\CAttModel;
use App\classroom;
use App\classroom\classtasks;
use App\classroom\collections;
use App\classroom\other;
use App\classroom\questions;
use App\classroom\statistics;
use App\classroom\wstasks;
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
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;


class ClassroomController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }




    /**
     * 
     * GENERAL CLASSROOM TASKS
     * 
     */

    public function join(Request $request)
    {
        return classtasks::join($request);
    }

    public function join_api(Request $request)
    {
        return classtasks::join_api($request);
    }

    public function index(Request $request) // View a classroom
    {
        return classtasks::index($request);
    }

    public function timeline(Request $request)
    {
        return classtasks::timeline($request);
    }

    public function people(Request $request)
    {
        return classtasks::people($request);
    }

    public function stream(Request $request)
    {
        return classtasks::stream($request);
    }

    public function sendinvite(Request $request)
    {
        return classtasks::sendinvite($request);
    }

    public function sendinvite_api(Request $request)
    {
        return classtasks::sendinvite_api($request);
    }
    public function listmembers(Request $request)
    {
        return classtasks::listmembers($request);
    }

    public function new(Request $request) // Create a classroom
    {
        return classtasks::new($request);
    }

    public function newsubmit(Request $request)
    {
        return classtasks::newsubmit($request);
    }

    public function validator(Request $request)
    {
        return classtasks::validator($request);
    }

    public function postnote(Request $request)
    {
        /**
         * User has posted a note.
         */

        $class = ClassroomModel::where('id', '=', $request->id)->first();
        if ($class === null) {
            return abort(404);
        }

        /*if(Auth::user()->id == $class->author){
            classroom::postitem_note($class->id, Auth::user()->username, $request->note);
            return redirect()->route('viewclassroom', [$class->id]);
        } else {
            return abort(403);
        }*/

        classroom::postitem_note($class->id, Auth::user()->username, $request->note);
        return redirect()->route('viewclassroom', [$class->id]);
    }

    public function delete(Request $request, $cid)
    {
        return classtasks::delete($request, $cid);
    }

    public function remove_user(Request $request, $cid, $username)
    {
        return classtasks::remove_user($request, $cid, $username);
    }

    public function remove_pendinguser(Request $request, $cid, $username)
    {
        return classtasks::remove_pendinguser($request, $cid, $username);
    }


    /**
     * 
     * 
     * QUESTION TASKS
     * 
     */

    public function postquestion(Request $request)
    {
        return questions::postquestion($request);
    }

    public function postquestion_validate(Request $request)
    {
        return questions::postquestion_validate($request);
    }
    public function postquestion_submit(Request $request)
    {
        return questions::postquestion_submit($request);
    }
    public function postquestion_api(Request $request)
    {
        return questions::postquestion_api($request);
    }
    public function answerquestion(Request $request, $id)
    {
        return questions::answerquestion($request, $id);
    }







    /**
     * 
     * 
     * WORKSHEET TASKS
     * 
     */

    public function postws(Request $request)
    {
        return wstasks::postws($request);
    }

    public function postws_validate(Request $request)
    {
        return wstasks::postws_validate($request);
    }

    public function postws_submit(Request $request)
    {
        return wstasks::postws_submit($request);
    }

    public function postws_api(Request $request)
    {
        return wstasks::postws_api($request);
    }

    public function preanswerws(Request $request, $cid, $wsname)
    {
        return wstasks::preanswerws($request, $cid, $wsname);
    }

    public function answerws(Request $request, $cid, $wsname)
    {
        return wstasks::answerws($request, $cid, $wsname);
    }

    public function pullcontentws(Request $request, $cid, $wsname)
    {
        return wstasks::pullcontentws($request, $cid, $wsname);
    }

    public function answerwssub(Request $request)
    {
        return wstasks::answerwssub($request);
    }

    public function postanswerws(Request $request, $cid, $wsname)
    {
        return wstasks::postanswerws($request, $cid, $wsname);
    }

    public function prevws(Request $request, $cid, $wsname)
    {
        return wstasks::prevws($request, $cid, $wsname);
    }


    /**
     * 
     * STATS
     * 
     */
    public function stats(Request $request, $cid)
    {
        return statistics::stats_page($request, $cid);
    }

    public function stats_reset(Request $request, $cid)
    {
        return statistics::stats_reset($request, $cid);
    }

    public function stats_attemptees(Request $request, $cid, $wsname)
    {
        return statistics::stats_attemptees($request, $cid, $wsname);
    }

    public function stats_userattempt(Request $request, $cid, $wsname, $uname)
    {
        return statistics::stats_userattempt($request, $cid, $wsname, $uname);
    }


    /**
     * 
     * OTHER TASKS
     * 
     */
    public function qedit(Request $request, $cid)
    {
        return other::qedit($request, $cid);
    }

    public function qremove(Request $request, $cid)
    {
        return other::qremove($request, $cid);
    }

    public function wsedit(Request $request, $cid)
    {
        return other::wsedit($request, $cid);
    }

    public function wsremove(Request $request, $cid)
    {
        return other::wsremove($request, $cid);
    }

    public function wseditsubmit(Request $request, $cid, $wsname)
    {
        return other::wseditsubmit($request, $cid, $wsname);
    }

    public function docupload(Request $request, $cid)
    {
        return other::docupload($request, $cid);
    }


    public function jsonupload(Request $request)
    {
        return other::jsonupload($request);
    }

    public function ws_getjson(Request $request, $id)
    {
        return other::ws_getjson($request, $id);
    }

    public function rename(Request $request, $cid)
    {
        return other::rename($request, $cid);
    }

    public function get_timeline(Request $request, $cid)
    {
        app('debugbar')->disable();
        return other::get_timeline($request, $cid);
    }

    /**
     * 
     * COLLECTION TASKS
     * 
     * 
     */

    public function newcollection(Request $request, $cid)
    {
        //return collections::new($request, $cid);
        return redirect()
            ->back()
            ->with(collections::new($request, $cid));
    }

    public function viewcollection(Request $request, $cid, $encname)
    {
        return collections::view($request, $cid, $encname);
    }

    public function renamecollection(Request $request, $cid, $encname)
    {
        return collections::rename($request, $cid, $encname);
    }

    public function deletecollection(Request $request, $cid, $encname)
    {
        return collections::delete($request, $cid, $encname);
    }


    //API SPECIFIC
    public function listws(Request $request)
    {
        $class = ClassroomModel::where("id", $request->cid)->first();
        if($class == null){
            return [
                "fucked" => true,
            ];
        }

        $dirname = $class->encname;

        $ws_name_list = [];
        foreach (Storage::allFiles("classrooms/" . $dirname . "/worksheets") as $wsname_entry) {
            array_push($ws_name_list, explode("worksheets/", $wsname_entry)[1]);
        }

        $ws_info_list = [];
        foreach ($ws_name_list as $wsname) {
            $current_wsinfo = json_decode(Storage::get("classrooms/" . $dirname . "/worksheets//" . $wsname), true);
            array_push($ws_info_list, [
                "name" => $wsname,
                "title" => $current_wsinfo['title']
            ]);
        }

        return $ws_info_list;
    }


    public function basicinfo(Request $request)
    {
        $class = ClassroomModel::where("id", $request->id)->first();
        if($class == null){
            return [
                "fucked" => true,
            ];
        }

        /**
         * Also, check if the logged in user is already a member
         */
        $members = classroom::memberlist($class->id);
        if(in_array(Auth::user()->username, $members)){
            $flag = true;
        } else {
            $flag = false;
        }

        return [
            "name" => $class->name,
            "author" => UserModel::where("id", $class->author)->first()->name,
            "joined" => $flag,
        ];
    }
}
