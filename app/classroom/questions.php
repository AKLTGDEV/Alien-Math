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


class questions
{
    public static function postquestion(Request $request)
    {
        $class = ClassroomModel::where('id', '=', $request->id)->first();
        if ($class === null) {
            return abort(404);
        }

        return view("classroom.post.question", [
            "class" => $class,
            "tags_suggested" => tags::top20(),
            "searchbar" => true,
        ]);
    }

    public static function postquestion_validate(Request $request)
    {
        $rules = array(
            'Qbody'             => 'required',
            'option1'           => 'required',
            'option2'           => 'required',
            'correct'           => 'required',
            'question_tags'     => ['required', 'string', new tagexists, new tags_min_2],
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Redirect::to(route('CLR_postq', [$request->id]))
                ->withErrors($validator)
                ->withInput(Input::all());
        } else {
            $CC = new ClassroomController();
            return $CC->postquestion_submit($request);
        }
    }
    public static function postquestion_submit(Request $request)
    {
        /**
         * User has posted a question.
         */

        $class = ClassroomModel::where('id', '=', $request->id)->first();
        if ($class === null) {
            return abort(404);
        }

        classroom::postitem_question($request->id, Auth::user()->username, $request);
        return redirect()->route('viewclassroom', [$class->id]);
    }
    public static function postquestion_api(Request $request)
    {
        /**
         * User has posted a question.
         */

        $class = ClassroomModel::where('id', '=', $request->id)->first();
        if ($class === null) {
            return [
                "fucked" => true,
                "msg" => "class doesn not exist"
            ];
        }

        $rules = array(
            'title'             => 'required',
            'Qbody'             => 'required',
            'option1'           => 'required',
            'option2'           => 'required',
            'correct'           => 'required',
            'question_tags'     => ['required', 'string', new tagexists, new tags_min_2],
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return [
                "fucked" => true,
                "msg" => "Error Processing Question.. Check your inputs",
                "errors" => $validator->messages(),
            ];
        } else {
            if (classroom::postitem_question($request->id, Auth::user()->username, $request)) {
                return [
                    "fucked" => false,
                    "msg" => "Question Posted"
                ];
            } else {
                return [
                    "fucked" => true,
                    "msg" => "Error"
                ];
            }
        }
    }
    public static function answerquestion(Request $request, $id)
    {
        /**
         * Check if the user has already answered 
         * the question or not. if not, let the user answer.
         * Otherwise, block.
         */

        $all = $request->all();
        $qname = $request->qname;
        $given = $request->given;

        //Check if the user has already attempted
        $prevattempt = CAttModel::where("classid", $id)
            ->where("name", $qname)
            ->where("attemptee", Auth::user()->username)
            ->first();
        if ($prevattempt != null) {
            return "FUCC";
        } else {
            /**
             * The user has not attempted this shiz.
             */

            $newattempt = new CAttModel;
            $newattempt->name = $qname;
            $newattempt->type = 1; // TYPE 1 --> POST
            $newattempt->body = $given;
            $newattempt->classid = $id;
            $newattempt->attemptee = Auth::user()->username;
            $newattempt->save();

            /**
             * Know whether the user answered correctly
             */

            $class = ClassroomModel::where("id", $id)->first();
            $dirname = $class->encname;
            $question_info = json_decode(Storage::get("classrooms/" . $dirname . "/questions//" . $qname), true);
            $corr =  $question_info['correct'];

            return ($corr == $given) ? "SUCCESS" : "FAILURE";
        }
    }
}
