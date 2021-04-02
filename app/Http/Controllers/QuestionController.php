<?php

namespace App\Http\Controllers;

use App\PostModel;
use Illuminate\Http\Request;
use App\SAQ;
use App\SQA;

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
}
