<?php

namespace App\Http\Controllers;

use App\PostModel;
use App\RatingsModel;
use App\Report;
use App\SAQ;
use App\SQA;
use App\Video;
use App\WorksheetModel;
use App\worksheets;
use App\wsAttemptsModel;
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
        $worksheet = WorksheetModel::where('slug', $slug)->first();

        if ($worksheet == null) {
            return abort(404);
        }

        $ws_info = json_decode(Storage::get("WS/$worksheet->ws_name"), true);
        $data = $ws_info['content'][$index - 1];

        $pending_att = wsAttemptsModel::where('wsid', $worksheet->id)
            ->where('attemptee', Auth::user()->id)
            ->first();

        $pending_att->clock_hit($request->hits);

        $r_stat = false;

        if ($request->type == "SAQ") {
            $pending_att->answer($request->answer);

            if ($request->answer != null) {
                if ($request->answer == $data['correct']) {
                    $pending_att->right++;
                    $pending_att->result("T");

                    $r_stat = true;
                } else {
                    $pending_att->wrong++;
                    $pending_att->result("F");
                }
            } else {
                $pending_att->left++;
                $pending_att->result("L");
            }

            $pending_att->save();

            /**
             * Fix the ratings
             * 
             */
            foreach ($data['topics'] as $tid) {
                RatingsModel::new(Auth::user()->username, $tid, 1000);

                $r = RatingsModel::where("of", Auth::user()->username)
                    ->where("topic", $tid)
                    ->first();
                $r->SAQ($data['id'], $r_stat);
            }


            /**
             * Get the videos
             */
            $videos = [];
            foreach (json_decode(SAQ::where("id", $data['id'])
                ->first()
                ->videos) as $v_id) {
                $videos[] = [
                    "id" => $v_id,
                    "loc" => route('video.stream', [$v_id]),
                ];
            }

            return [
                "correct" => $data['correct'],
                "explanation" => $data['explanation'],
                "videos" => $videos,
            ];
        } else if ($request->type == "MCQ") {
            if ($request->answer != null) {
                $pending_att->answer($request->answer);
                if ($request->answer == $data['correct']) {
                    $pending_att->right++;
                    $pending_att->result("T");

                    $r_stat = true;
                } else {
                    $pending_att->wrong++;
                    $pending_att->result("F");
                }

                $answers = $request->answer;
                $actual_answer = $answers[count($answers) - 1];
                $pending_att->answer($actual_answer);
            } else {
                $pending_att->left++;
                $pending_att->result("L");
                $pending_att->answer(null); //OR 0??
            }

            $pending_att->save();

            /**
             * We get a "ans" input, which contains all the 
             * option changes of the user. The last one is the final answer.
             * 
             */

            $correct = $data['opts'][$data['correct'] - 1];

            /**
             * Fix the ratings
             * 
             */
            foreach ($data['topics'] as $tid) {
                RatingsModel::new(Auth::user()->username, $tid, 1000);

                $r = RatingsModel::where("of", Auth::user()->username)
                    ->where("topic", $tid)
                    ->first();
                $r->SAQ($data['id'], $r_stat);
            }

            /**
             * Get the videos
             */
            $videos = [];
            foreach (json_decode(PostModel::where("id", $data['id'])
                ->first()
                ->videos) as $v_id) {
                $videos[] = [
                    "id" => $v_id,
                    "loc" => route('video.stream', [$v_id]),
                ];
            }

            return [
                "correct" => $correct,
                "explanation" => $data['explanation'],
                "videos" => $videos,
            ];
        } else if ($request->type == "SQA") {

            //return $request->all();

            if ($request->answer != null) {
                $pending_att->answer($request->answer);

                // Some other conditions here

                $pending_att->right++;
                $flag = true;
                for ($i = 1; $i <= count($data['opts']); $i++) {
                    $current_key = $data['opts'][$i - 1];

                    if ($request->answer[$current_key] == $i) {
                        // Do nothing
                    } else {
                        $flag = false;
                        break;
                    }
                }

                if ($flag) {
                    $pending_att->right++;
                    $pending_att->result("T");

                    $r_stat = true;
                } else {
                    $pending_att->wrong++;
                    $pending_att->result("F");
                }
            } else {
                $pending_att->left++;
                $pending_att->result("L");
                $pending_att->answer($request->answer);
            }

            $pending_att->save();

            /**
             * Fix the ratings
             * 
             */
            foreach ($data['topics'] as $tid) {
                RatingsModel::new(Auth::user()->username, $tid, 1000);

                $r = RatingsModel::where("of", Auth::user()->username)
                    ->where("topic", $tid)
                    ->first();
                $r->SAQ($data['id'], $r_stat);
            }

            /**
             * Get the videos
             */
            $videos = [];
            foreach (json_decode(SQA::where("id", $data['id'])
                ->first()
                ->videos) as $v_id) {
                $videos[] = [
                    "id" => $v_id,
                    "loc" => route('video.stream', [$v_id]),
                ];
            }

            return [
                "correct" => $data['opts'],
                "explanation" => $data['explanation'],
                "videos" => $videos,
            ];
        }
    }

    public function report(Request $request, $slug)
    {
        $worksheet = WorksheetModel::where('slug', $slug)->first();

        if ($worksheet == null) {
            return abort(404);
        }

        $ws_info = json_decode(Storage::get("WS/$worksheet->ws_name"), true);
        $data = $ws_info['content'][$request->id - 1];

        $rep = new Report;
        $rep->type = $data['type'];
        $rep->item_id = $worksheet->id;
        $rep->ws_qid = $request->id;
        $rep->from = Auth::user()->username;
        $rep->data = $request->body;
        $rep->save();


        return [
            "status" => true,
        ];
    }
}
