<?php

namespace App\Http\Controllers;

use App\PostModel;
use App\worksheets;
use Illuminate\Http\Request;

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
        //Collect $nos MCQs

        $list = [];

        $interim = PostModel::where("type", $request->grade)
            ->where("difficulty", $request->difficulty)
            ->get();

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
}
