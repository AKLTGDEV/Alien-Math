<?php

namespace App\Http\Controllers;

use App\posts;
use App\qb;
use App\QBquestionsModel;
use App\QBSubTopicsModel;
use App\QBTestsModel;
use App\QBTopicsModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use QbSubtopics;
use Symfony\Component\HttpFoundation\Request;

class QBController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $qlist_flag = false;
        $qlist = [];
        if (
            Session::has('QB_PENDING_TEST') &&
            Session::get('QB_PENDING_TEST') == true
        ) {
            $qlist = Session::get('QB_LIST');
            $qlist_flag = true;
        }

        return view("qb.index", [
            "user" => Auth::user(),
            "qlist_flag" => $qlist_flag,
            "qlist" => $qlist,
            "QB_NAV_PAGE" => "INDEX",
            "searchbar" => true,
        ]);
    }

    public function questions(Request $request)
    {
        app('debugbar')->disable();
        return qb::call(Auth::user(), $request);
    }

    public function index_topics(Request $request)
    {
        $topics = QBTopicsModel::where("author", Auth::user()->id)->get();

        return view("qb.topics", [
            "user" => Auth::user(),
            "topics" => $topics,
            "QB_NAV_PAGE" => "TOPICS",
            "searchbar" => true,
        ]);
    }

    public function addtopic(Request $request)
    {
        $topic = new QBTopicsModel;
        $topic->generic(Auth::user()->id, $request->name);
        return redirect()->route('qbank_index_topics');
    }

    public function index_subtopics(Request $request)
    {
        $topics = QBTopicsModel::where("author", Auth::user()->id)->get();
        $subtopics = QBSubTopicsModel::where("author", Auth::user()->id)->get();

        $subtopics_final = [];
        foreach ($subtopics as $st) {
            $st->parent_obj = QBTopicsModel::where("id", $st->parent)->first();
            array_push($subtopics_final, $st);
        }

        $view_array = [
            "user" => Auth::user(),
            "topics" => $topics,
            "subtopics" => $subtopics_final,
            "QB_NAV_PAGE" => "SUBTOPICS",
            "searchbar" => true,
        ];

        if (count($topics) == 0) {
            $view_array['insuff_err'] = true;
            return view("qb.subtopics", $view_array)
                ->withErrors([
                    "You need to create At least 1 Topic to create a sub-topic"
                ]);
        } else {
            $view_array['insuff_err'] = false;
            return view("qb.subtopics", $view_array);
        }
    }

    public function addsubtopic(Request $request)
    {
        $topic = new QBSubTopicsModel;
        $topic->generic(Auth::user()->id, $request->name, $request->parent);
        return redirect()->route('qbank_index_subtopics');
    }

    public function newq_mcq(Request $request)
    {
        $topics = QBTopicsModel::where("author", Auth::user()->id)->get();
        $subtopics = QBSubTopicsModel::where("author", Auth::user()->id)->get();

        if (count($topics) + count($subtopics) <= 1) {
            return redirect()
                ->back()
                ->withErrors([
                    "You need to create At least 1 Topic and 1 Sub-topic to create a question"
                ]);
        }

        return view("qb.question.mcq", [
            "user" => Auth::user(),
            "topics" => $topics,
            "searchbar" => true,
        ]);
    }

    public function newq_mcq_validate(Request $request)
    {
        $rules = array(
            'Qbody'         => 'required',
            //'title'             => 'required',
            'correct'       => 'required',
            'topic'         => ['required', 'integer'],
            'subtopic'      => ['required', 'integer'],
        );

        for ($i = 1; $i <= $request->opt_nos; $i++) {
            $rules["option$i"] = "required";
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput(Input::all());
        } else {
            /*$PC = new PostController();
            return $PC->newsubmit($request);*/

            return qb::new_mcq($request);
        }
    }

    public function list_subtopics(Request $request)
    {
        $topic = QBTopicsModel::where("author", Auth::user()->id)
            ->where("id", $request->topic)
            ->first();

        if ($topic != null) {
            $subtopics = QBSubTopicsModel::where("author", Auth::user()->id)
                ->where("parent", $topic->id)
                ->get();

            $subtopics_final = [];

            foreach ($subtopics as $st) {
                array_push($subtopics_final, [
                    "id" => $st->id,
                    "name" => $st->name
                ]);
            }

            return [
                "status" => "ok",
                "st" => $subtopics_final
            ];
        } else {
            return [
                "status" => "error",
                "msg" => "topic now found"
            ];
        }
    }

    public function newq_subjective(Request $request)
    {
        $topics = QBTopicsModel::where("author", Auth::user()->id)->get();
        $subtopics = QBSubTopicsModel::where("author", Auth::user()->id)->get();

        if (count($topics) + count($subtopics) <= 1) {
            return redirect()
                ->back()
                ->withErrors([
                    "You need to create At least 1 Topic and 1 Sub-topic to create a question"
                ]);
        }

        return view("qb.question.subjective", [
            "user" => Auth::user(),
            "topics" => $topics,
            "searchbar" => true,
        ]);
    }

    public function newq_subjective_validate(Request $request)
    {
        $rules = array(
            'Qbody'         => 'required',
            'title'         => 'string',
            'topic'         => ['required', 'integer'],
            'subtopic'      => ['required', 'integer'],
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput(Input::all());
        } else {
            return qb::new_subjective($request);
        }
    }

    // Topic/Subtopic index

    public function topic(Request $request, $topicid)
    {
        $topic = QBTopicsModel::where("id", $topicid)->first();

        return view("qb.view.topic", [
            "user" => Auth::user(),
            "topic" => $topic,
            "QB_NAV_PAGE" => "PART_TOPIC",
            "searchbar" => true,
        ]);
    }

    public function list_topic(Request $request, $topicid)
    {
        app('debugbar')->disable();
        $idx = json_decode($request->idx, true);
        $content = qb::get_under_topic(Auth::user(), $idx, $topicid);
        return [
            'result' => $content['result'],
            'idx' => $content['idx'],
        ];
    }

    public function subtopic(Request $request, $subtopicid)
    {
        $subtopic = QBSubTopicsModel::where("id", $subtopicid)->first();

        return view("qb.view.subtopic", [
            "user" => Auth::user(),
            "subtopic" => $subtopic,
            "QB_NAV_PAGE" => "PART_SUBTOPIC",
            "searchbar" => true,
        ]);
    }

    public function list_subtopic(Request $request, $subtopicid)
    {
        app('debugbar')->disable();
        $idx = json_decode($request->idx, true);
        $content = qb::get_under_subtopic(Auth::user(), $idx, $subtopicid);
        return [
            'result' => $content['result'],
            'idx' => $content['idx'],
        ];
    }

    public function index_tests(Request $request)
    {
        $qlist_flag = false;
        $qlist = [];
        if (
            Session::has('QB_PENDING_TEST') &&
            Session::get('QB_PENDING_TEST') == true
        ) {
            $qlist = Session::get('QB_LIST');
            $qlist_flag = true;
        }

        $tlist = QBTestsModel::where("author", Auth::user()->id)->get();
        $tlist_final = [];
        foreach ($tlist as $t) {
            array_push($tlist_final, $t->content());
        }

        return view("qb.tests", [
            "user" => Auth::user(),
            "qlist_flag" => $qlist_flag,
            "qlist" => $qlist,
            "tlist" => $tlist_final,
            "QB_NAV_PAGE" => "TESTS",
            "searchbar" => true,
        ]);
    }

    public function tests_addq(Request $request)
    {
        $qlist = json_decode($request->qlist, true);
        if (count($qlist) > 0) {
            Session::put('QB_PENDING_TEST', true);
            Session::put('QB_LIST', $qlist);

            return ['status' => "ok",];
        } else {
            return [
                'status' => "error",
                "msg" => "select some questions to proceed"
            ];
        }
    }

    public function tests_finalize(Request $request)
    {
        $qlist_flag = false;
        if (
            Session::has('QB_PENDING_TEST') &&
            Session::get('QB_PENDING_TEST') == true
        ) {
            $qlist = Session::get('QB_LIST');
            $qlist_flag = true;
        }

        $qlist_final = [];
        foreach ($qlist as $q) {
            $qid = $q['ID'];

            if ($q['TYPE'] == "PVT") {
                $q_pvt = QBquestionsModel::where("id", $qid)->first();
                array_push($qlist_final, $q_pvt->content());
            } else if ($q['TYPE'] == "PUBLIC") {
                array_push($qlist_final, posts::get($qid));
            }
        }

        $topics = QBTopicsModel::where("author", Auth::user()->id)->get();

        return view("qb.pending-finalize", [
            "user" => Auth::user(),
            "qlist_flag" => $qlist_flag,
            "qlist" => $qlist_final,
            "topics" => $topics,
            "QB_NAV_PAGE" => "TESTS",
            "searchbar" => true,
        ]);
    }

    public function tests_finalize_submit(Request $request)
    {
        if (
            Session::has('QB_PENDING_TEST') &&
            Session::get('QB_PENDING_TEST') == true
        ) {
            // Okay, Proceed
            $qlist = Session::get('QB_LIST');
        } else {
            redirect()->route('qbank_index_tests');
        }
        /**
         * STEP 1: Validate input
         * STEP 2: Make entry in DB
         * STEP 3: Store in local storage
         * STEP 4: Delete Session entry
         * STEP 5: Return to Tests page
         */

        $rules = array(
            'title'         => 'string',
            'topic'         => ['required', 'integer'],
            'subtopic'      => ['required', 'integer'],
        );

        for ($i = 1; $i <= count($qlist); $i++) {
            $rules["marks-$i"] = ['required', 'integer'];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput(Input::all());
        } else {
            $all = $request->all();

            //return $all;

            // Make an entry in the DB table
            $question = new QBTestsModel;
            $encname = $question->make(
                Auth::user()->id,
                $request->title,
                $request->topic,
                $request->subtopic
            );

            $content = [];
            foreach ($qlist as $q) {
                $qid = $q['ID'];

                if ($q['TYPE'] == "PVT") {
                    $q_pvt = QBquestionsModel::where("id", $qid)->first();
                    array_push($content, $q_pvt->content());
                } else if ($q['TYPE'] == "PUBLIC") {
                    array_push($content, posts::get($qid));
                }
            }
            $all['content'] = $content;

            //Store data in local storage
            Storage::put("QB_tests/$encname", json_encode($all));

            $request->session()->forget('QB_PENDING_TEST');
            $request->session()->forget('QB_LIST');

            return redirect()->route('qbank_index_tests');
        }
    }

    public function tests_getpdf(Request $request, $id)
    {
        $test = QBTestsModel::where("id", $id)->first();
        $content = $test->content();

        $html = view("qb.test-pdf", [
            "user" => Auth::user(),
            "content" => $content,
            "searchbar" => true,
        ])->render();

        //Render to PDF
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.pdfshift.io/v2/convert/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode(array(
                "source" => $html,
                "landscape" => false,
                "use_print" => false,
                //"sandbox" => true,
            )),
            CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
            CURLOPT_USERPWD => '31120451d5c146f487fc759796ca5667:'
        ));
        $response = curl_exec($curl);
        $fileName = $test->title . ".pdf";
        $headers = [
            'Content-type' => 'text/plain',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName),
            'Content-Length' => strlen($response)
        ];
        return Response::make($response, 200, $headers);
    }
}
