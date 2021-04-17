<?php

namespace App\Http\Controllers;

use App\PostModel;
use App\Report;
use Illuminate\Http\Request;
use App\SAQ;
use App\SQA;
use App\Video;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function view_mcq(Request $request, $id)
    {
        $q = PostModel::where("id", $id)->first();

        if ($q != null) {
            //return redirect()->route("viewpost", [$id]);
            return view("question.mcq", [
                "question" => $q,
                "type" => "MCQ",
                "videos" => $q->videos(),
            ]);
        } else {
            return abort(404);
        }
    }

    public function view_saq(Request $request, $id)
    {
        $q = SAQ::where("id", $id)->first();

        if ($q != null) {
            return view("question.saq", [
                "question" => $q,
                "type" => "SAQ",
                "videos" => $q->videos(),
            ]);
        } else {
            return abort(404);
        }
    }

    public function view_sqa(Request $request, $id)
    {
        $q = SQA::where("id", $id)->first();

        //return $q;

        if ($q != null) {
            return view("question.sqa", [
                "question" => $q,
                "type" => "SQA",
                "videos" => $q->videos(),
            ]);
        } else {
            return abort(404);
        }
    }


    /**
     * REPORT METHODS
     * 
     * Available to teachers, students. Admins would get a notification
     * 
     */

    public function report_mcq(Request $request, $id)
    {
        $q = PostModel::where("id", $id)->first();

        if ($q != null) {
            return view("report", [
                "question" => $q,
                "type" => "MCQ"
            ]);
        } else {
            return abort(404);
        }
    }

    public function report_saq(Request $request, $id)
    {
        $q = SAQ::where("id", $id)->first();

        if ($q != null) {
            return view("report", [
                "question" => $q,
                "type" => "SAQ"
            ]);
        } else {
            return abort(404);
        }
    }

    public function report_sqa(Request $request, $id)
    {
        $q = SQA::where("id", $id)->first();

        if ($q != null) {
            return view("report", [
                "question" => $q,
                "type" => "SQA"
            ]);
        } else {
            return abort(404);
        }
    }

    public function report_submit(Request $request)
    {
        $report = new Report;
        $report->type = $request->type;
        $report->item_id = $request->id;
        $report->data = $request->body;
        $report->from = Auth::user()->username;

        $report->save();
        return redirect()->route('home');
    }

    public function attach_videos($type, $id)
    {
        /**
         * Attach videos to Question ID $id
         * 
         * Leave out the videos that are already attached
         */

        $videos_fin = [];
        $videos = Video::where("uploader", Auth::user()->username)
            ->orderBy('id', 'desc')
            ->get(); // Get all videos

        switch ($type) {
            case 'MCQ':
                $q = PostModel::where("id", $id)->first();
                foreach ($videos as $v) {
                    if (!in_array($id, $v->getMCQs())) {
                        $videos_fin[] = $v;
                    }
                }
                break;

            case 'SAQ':
                $q = SAQ::where("id", $id)->first();
                foreach ($videos as $v) {
                    if (!in_array($id, $v->getSAQs())) {
                        $videos_fin[] = $v;
                    }
                }
                break;

            case 'SQA':
                $q = SQA::where("id", $id)->first();
                foreach ($videos as $v) {
                    if (!in_array($id, $v->getSQAs())) {
                        $videos_fin[] = $v;
                    }
                }
                break;

            default:
                # code...
                break;
        }

        return view("question.attach-video", [
            "question" => $q,
            "videos" => $videos_fin,
        ]);
    }

    public function gateway_add()
    {
        return view("question.gateway.add", [
            //
        ]);
    }

    public function gateway_upload()
    {
        return view("question.gateway.upload", [
            //
        ]);
    }
}
