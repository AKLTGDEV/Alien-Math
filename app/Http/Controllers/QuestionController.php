<?php

namespace App\Http\Controllers;

use App\PostModel;
use App\Report;
use Illuminate\Http\Request;
use App\SAQ;
use App\SQA;
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
            ]);
        } else {
            return abort(404);
        }
    }

    public function view_saq(Request $request, $id)
    {
        $q = SAQ::where("id", $id)->first();

        //return $q;

        if ($q != null) {
            return view("question.saq", [
                "question" => $q,
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
}
