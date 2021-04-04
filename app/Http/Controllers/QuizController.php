<?php

namespace App\Http\Controllers;

use App\PostModel;
use App\SAQ;
use App\SQA;
use App\WorksheetModel;
use App\worksheets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class QuizController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function generate(Request $request)
    {
        /**
         * 
         * NOTE
         * 
         * This class would generate a standard worksheet from the given inputs.
         * (Level, Grade, etc) After a handful of questions are selected and the
         * WS is stored, the browser will re-direct to the ws-preanswer route.
         * Everything goes normaly from there.
         * 
         */

        $nos = $request->nos;
        $list = [];
        $interim = [];

        foreach (PostModel::where("type", $request->grade)
            ->where("difficulty", $request->difficulty)
            ->get() as $p) {
            $interim[] = $p;
        }

        foreach (SAQ::where("type", $request->grade)
            ->where("difficulty", $request->difficulty)
            ->get() as $p) {
            $interim[] = $p;
        }

        foreach (SQA::where("type", $request->grade)
            ->where("difficulty", $request->difficulty)
            ->get() as $p) {
            $interim[] = $p;
        }

        shuffle($interim);

        $i = 0;
        while (count($list) < $nos) {
            if ($i + 1 > count($interim)) {
                break;
            }

            $list[] = $interim[$i];
            $i++;
        }

        /**
         * $list contains all the MCQs. Generate a WS from those MCQs
         * 
         */

        $q_list = [];
        foreach ($list as $q) {
            $q_list[] = $q->info();
        }

        return redirect()->route('wsanswer-1', [
            worksheets::quiz(
                "Sample Quiz",
                ["JEE", "NEET"],
                count($list),
                10,

                $q_list,
                1
            )
        ]);
    }

    public function pullcontent_byindex($slug, $index)
    {
        $worksheet = WorksheetModel::where('slug', $slug)->first();

        if ($worksheet == null) {
            return abort(404);
        }

        $ws_info = json_decode(Storage::get("WS/$worksheet->ws_name"), true);
        $self = Auth::user();

        return [
            "status" => "ok",
            "data" => $ws_info['content'][$index - 1],
        ];


        /**
         * FIXME: ENSURE THAT THE TEST IS ALREADY IN PROGRESS
         * 
         */
        //TODO FIXME
    }

    public function singleanswer(Request $request, $slug, $index)
    {
        /*return [
            $request->all(),
            $slug,
            $index,
        ];*/

        $worksheet = WorksheetModel::where('slug', $slug)->first();

        if ($worksheet == null) {
            return abort(404);
        }

        $ws_info = json_decode(Storage::get("WS/$worksheet->ws_name"), true);
        $data = $ws_info['content'][$index - 1];

        if ($request->type == "SAQ") {
            return [
                "correct" => $data['correct'],
                "explanation" => $data['explanation'],
            ];
        } else if ($request->type == "MCQ") {
            /**
             * We get a "ans" input, which contains all the 
             * option changes of the user. The last one is the final answer.
             * 
             */

            $correct = $data['opts'][$data['correct'] + 1];

            return [
                "correct" => $correct,
                "explanation" => $data['explanation'],
            ];
        } else if ($request->type == "SQA") {
            /**
             * We get "ans", which is the given order.
             */

            return [
                "correct" => $data['opts'],
                "explanation" => $data['explanation'],
            ];
        }
    }
}
