<?php

namespace App\Http\Controllers;

use App\PostModel;
use App\posts;
use App\rating;
use App\TagsModel;
use Illuminate\Support\Facades\Auth;
use App\WorksheetModel;
use App\UserModel;
use App\worksheets;
use App\users;
use App\wsAttemptsModel;
use Illuminate\Support\Facades\Storage;


class StatsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /*public function view()
    {
        $__posts = PostModel::where('author', Auth::user()->id)->get();
        //$answerlist = json_decode(Auth::user()->answers, true);
        $answerlist = json_decode(Storage::get('answers/' . Auth::user()->username), true);

        $right = 0;
        $tot = 0;
        foreach ($answerlist as $q => $a) {
            $qno = explode("q", $q)[1];
            if (posts::check_rightwrong_pid(Auth::user(), $qno) == true) {
                $right++;
            }
            $tot++;
        }

        
        // Get the stats for most used topics
        
        $user_posts = posts::list(Auth::user()->username);
        $tags_used = array();
        foreach ($user_posts as $p) {
            $p_tags = json_decode($p["tags"], true);
            foreach ($p_tags as $tagname) {
                array_push($tags_used, $tagname);
            }
        }
        $tags_used = array_count_values($tags_used);
        asort($tags_used);
        $tags_used = array_reverse($tags_used);


        //Same as above, but for answered questions
        $tags_ans = array();
        foreach (posts::list_answered(Auth::user()) as $qno) {
            $post = PostModel::where('id', $qno)->first();
            $tags = json_decode($post->tags, true);
            foreach ($tags as $tagname) {
                array_push($tags_ans, $tagname);
            }
        }
        $tags_ans = array_count_values($tags_ans);
        asort($tags_ans);
        $tags_ans = array_reverse($tags_ans);

        //Get all the worksheets posted/attempted by the user with its ids.
        $__worksheets = StatsController::get_all_ws();
        //dd($__worksheets);
        $worksheets = array();
        foreach ($__worksheets as $ws) {
            array_push($worksheets, [$ws->id, $ws->title]);
        }

        //get only the worksheets posted by the user
        $self_posted = WorksheetModel::where("author", Auth::user()->id)->get();
        // FIXME Change this method of retrieving the posted WS

        return view('statsview', [
            "posts" => sizeof($__posts),
            "answers" => sizeof($answerlist),
            "aggregate" => $tot == 0 ? 0 : round(($right / $tot) * 100, 2),
            "rating" => Auth::user()->rating,
            "tags_posted" => $tags_used,
            "tags_answered" => $tags_ans,
            "worksheets" => $worksheets,
            "self_worksheets" => $self_posted,
            "daily_record" => rating::get_dr(Auth::user()),

            "searchbar" => true
        ]);
    }*/

    public function view()
    {
        $__posts = PostModel::where('author', Auth::user()->id)->get();

        // Get the stats for most used topics

        $user_posts = posts::list(Auth::user()->username);
        $tags_used = array();
        foreach ($user_posts as $p) {
            $p_tags = json_decode($p["tags"], true);
            foreach ($p_tags as $tagname) {
                array_push($tags_used, $tagname);
            }
        }
        $tags_used = array_count_values($tags_used);
        asort($tags_used);
        $tags_used = array_reverse($tags_used);


        $self_posted = WorksheetModel::where("author", Auth::user()->id)
            ->where("attempts", ">", 0)
            ->get();


        if (Auth::user()->isTeacher()) {
            return view('stats.teacher', [
                "posts" => sizeof($__posts),
                "tags_posted" => $tags_used,
                "worksheets" => $self_posted,
                "daily_record" => rating::get_dr(Auth::user()),

                "searchbar" => true
            ]);
        } else if (Auth::user()->isStudent()) {
            /**
             * Get a list of worksheets the user has attempted
             * 
             */
            $wslist = [];
            $attempts = wsAttemptsModel::where("attemptee", Auth::user()->id)->get();
            foreach ($attempts as $att) {
                $wslist[] = WorksheetModel::where("id", $att->wsid)->first();
            }

            return view('stats.student', [
                "worksheets" => $wslist,
                "daily_record" => rating::get_dr(Auth::user()),
                "user" => Auth::user(),

                "searchbar" => true
            ]);
        } else if (Auth::user()->isAdmin()) {
            return "ADMIN";
        }
    }

    public function get_ws_attemptees($wsid)
    {
        //FIXME check if the person requesting is the author of the WS or not.
        if (worksheets::exists($wsid)) {

            /**
             * If the person requesting is not the author 
             * but has attempted the WS, only return his own entry.
             * 
             */

            $ws = WorksheetModel::where('id', $wsid)->first();
            /*if ($ws->author != Auth::user()->id) {
                // User is not the author. If he's an attemptee, return his name.
                $att_self = wsAttemptsModel::where("attemptee", Auth::user()->id)->first();
                if ($att_self == null) {
                    // User has not attempted.
                    return ["status" => "error"];
                } else {
                    $ret = [];
                    array_push($ret, [Auth::user()->username, Auth::user()->name]);
                    return $ret;
                }
            } else {

                $ret = [];
                $attempts = wsAttemptsModel::where("wsid", $ws->id)->get();

                foreach ($attempts as $a) {
                    $U = UserModel::where('id', $a->attemptee)->first();
                    $ret[] = [
                        $U->username,
                        $U->name,
                    ];
                }

                return $ret;
        }*/

            $ret = [];
            $attempts = wsAttemptsModel::where("wsid", $ws->id)->get();

            foreach ($attempts as $a) {
                $U = UserModel::where('id', $a->attemptee)->first();
                $ret[] = [
                    $U->username,
                    $U->name,
                ];
            }

            return $ret;
        } else {
            return ["status" => "error"];
        }
    }

    public function get_all_ws()
    {
        $ws = [];
        $posted = WorksheetModel::where('author', Auth::user()->id)->get();
        /**
         * NEW: GET all the WS the user has attempted as well
         */
        $user_ws_attempts = wsAttemptsModel::where("attemptee", Auth::user()->id)->get();

        foreach ($posted as $w) {
            array_push($ws, $w);
        }
        foreach ($user_ws_attempts as $w_att) {
            $interim_ws = WorksheetModel::where("id", $w_att->wsid)->first();
            array_push($ws, $interim_ws);
        }

        return array_unique($ws);
    }

    static public function stats_ws_user($wsid, $uname)
    {
        $U = UserModel::where('username', $uname)->first();
        $uid = $U->id;
        if (worksheets::exists($wsid) && users::exists($uid)) {

            $ws = WorksheetModel::where('id', $wsid)->first();

            if (worksheets::attempted($U, $ws)) {
                $attempt = wsAttemptsModel::where('wsid', $wsid)
                    ->where('attemptee', $uid)
                    ->first();
                // The user has attempted the WS. Get correct and wrong answers.

                $worksheet = WorksheetModel::where('id', $wsid)->first();
                if ($worksheet == null) {
                    return abort(404);
                }

                $wsa_metrics = [];


                //OPT CHANGES
                $wsa_metrics['opt_changes'] = [];


                //CLOCK HITS
                $wsa_metrics_hits = [];
                $secs = 0;
                foreach (json_decode(Storage::get("wsa_metrics/$attempt->id/clock_hits")) as $hit) {
                    $wsa_metrics_hits[] = $hit;
                    $secs += $hit;
                }
                $wsa_metrics['clock_hits'] = $wsa_metrics_hits;

                $results__ = array_count_values($attempt->results());

                $right = 0;
                $wrong = 0;
                $left = 0;

                if (array_key_exists("T", $results__)) {
                    $right = $results__['T'];
                }

                if (array_key_exists("F", $results__)) {
                    $wrong = $results__['F'];
                }

                if (array_key_exists("L", $results__)) {
                    $left = $results__['L'];
                }

                $topics = [];

                //$results = json_decode($attempt->results);
                $results = $attempt->results();
                $ws_info = json_decode(Storage::get("WS/$ws->ws_name"), true);
                $questions = $ws_info['content'];
                foreach ($ws->topics() as $t) {
                    $topic = TagsModel::where("name", $t)->first();

                    /**
                     * Stats Under each topic:
                     * 
                     * 1. % of questions attempted successfully
                     * 2. % of questions left
                     * 3. Average time spent
                     * 4. Other info (TODO)
                     * 
                     */

                    $right = 0;
                    $wrong = 0;
                    $left = 0;

                    $i = 0;

                    $net_q = 0;
                    foreach ($questions as $q) {
                        if (in_array($topic->id, $q['topics'])) {
                            // Current topic IS atatched with this question
                            $net_q++;

                            if (count($results) < $i + 1) {
                                $left++;
                            } else {
                                switch ($results[$i]) {
                                    case 'T':
                                        $right++;
                                        break;
                                    case 'F':
                                        $wrong++;
                                        break;
                                    case 'L':
                                        $left++;
                                        break;

                                    default:
                                        // This should not be happening
                                        break;
                                }
                            }
                        }

                        $i++;
                    }

                    $topics[$topic->name] = [
                        "right" => round(($right / $net_q) * 100, 3),
                        "left" => round(($left / $net_q) * 100, 3),
                    ];
                }

                return [
                    "status" => "success",
                    "general" => [
                        "wsid"  => $ws->id,
                        "right" => $right,
                        "wrong" => $wrong,
                        "left" => $left,
                        "nos" => $ws->nos,
                    ],
                    "metrics" => $wsa_metrics,
                    "answers" => $attempt->getanswers(),
                    "secs" => $secs,
                    //"results" => json_decode($attempt->results),
                    "results" => $attempt->results(),
                    "topics" => $topics,
                ];
            } else {
                return [
                    "status" => "success",
                    "msg" => "not attempted"
                ];
            }
        }

        return [
            "status" => "error",
            "msg" => "..."
        ];
    }

    static public function stats_ws_publicuser($wsid, $publicid)
    {
        //$U = UserModel::where('username', $uname)->first();
        //$uid = $U->id;
        if (!worksheets::exists($wsid)) {
            return [
                "status" => "error",
                "msg" => "..."
            ];
        }

        $ws = WorksheetModel::where('id', $wsid)->first();
        $attempt = wsAttemptsModel::where('wsid', $wsid)
            ->where('public_id', $publicid)
            ->first();
        // The user has attempted the WS. Get correct and wrong answers.

        $worksheet = WorksheetModel::where('id', $wsid)->first();
        if ($worksheet == null) {
            return abort(404);
        }

        $ws_info = json_decode(Storage::get("WS/$worksheet->ws_name"));

        $att_answers = json_decode($attempt->answers);
        //$cor_answers = json_decode($ws->correctopts);
        $cor_answers = $ws_info->correct;

        $right = 0;
        $wrong = 0;
        $left = 0;

        $results = array();

        for ($i = 0; $i <= count($cor_answers) - 1; $i++) {
            if ($att_answers[$i] == "N") {
                $left++;
                $results[$i] = "L"; //LEFT
            } else if ($att_answers[$i] == $cor_answers[$i]) {
                $right++;
                $results[$i] = "T"; //Correct
            } else {
                $wrong++;
                $results[$i] = "F"; //Wrong
            }
        }

        if ($right + $wrong + $left == count($cor_answers)) {
            /**
             * Grab the metrics data from the Filesystem.
             */
            $att_id = $attempt->wsid . "." . $attempt->public_id;
            $metrics = Storage::get('wsa_metrics/' . $att_id);
            return [
                "status" => "success",
                "general" => [
                    "wsid"  => $ws->id,
                    "right" => $right,
                    "wrong" => $wrong,
                    "left" => $left
                ],
                "metrics" => json_decode($metrics, true),
                "answers" => json_decode($attempt->answers, true),
                "results" => $results
            ];
        } else {
            return [
                "status" => "error",
                "msg" => "..."
            ];
        }
    }

    public function stats_ws($wsid)
    {
        if (worksheets::exists($wsid)) {
            $ws = WorksheetModel::where('id', $wsid)->first();
            $cor_answers = json_decode($ws->correctopts);
            $atts = wsAttemptsModel::where('wsid', $wsid)->get();

            $MASTER_right = 0;
            $MASTER_wrong = 0;
            $MASTER_left = 0;

            foreach ($atts as $attempt) {
                $right = 0;
                $wrong = 0;
                $left = 0;
                $att_answers = json_decode($attempt->answers);

                for ($i = 0; $i <= count($cor_answers) - 1; $i++) {
                    if ($att_answers[$i] == "N") {
                        $left++;
                    } else if ($att_answers[$i] == $cor_answers[$i]) {
                        $right++;
                    } else {
                        $wrong++;
                    }
                }

                $MASTER_right += $right;
                $MASTER_wrong += $wrong;
                $MASTER_left  += $left;
            }

            $att_nos = $ws->attempts;
            if ($MASTER_right + $MASTER_wrong + $MASTER_left == (count($cor_answers) * $att_nos)) {
                return [
                    "status" => "success",
                    "general" => [
                        "wsid"  => $ws->id,
                        "right" => $MASTER_right,
                        "wrong" => $MASTER_wrong,
                        "left" => $MASTER_left
                    ]
                ];
            } else {
                return [
                    "status" => "error",
                    "msg" => "..."
                ];
            }
        }
    }

    public function ws_q_details($wsid, $q)
    {
        $ws = WorksheetModel::where('id', $wsid)->first();
        $ws_info = json_decode(Storage::get("WS/$ws->ws_name"), true);
        $data = $ws_info['content'][$q - 1];

        /**
         * 
         * General data to be returned for each question:
         * 
         * 1. % of attemptees who got it right
         * 2. %of users who left it
         * 3. Average attempt time
         */

        $all_attempts = wsAttemptsModel::where("wsid", $ws->id)
            ->get();

        $left = 0;
        $correct = 0;

        $hits = 0;

        foreach ($all_attempts as $att) {
            // PART 1: RESULTS
            $results = $att->results();
            if ($q > count($results)) {
                $left++;
            } else {
                $r = $results[$q - 1];
                if ($r == "L") {
                    $left++;
                } else if ($r == "T") {
                    $correct++;
                }
            }


            //PART 2: CLOCK HITS
            if ($q > count($results)) {
                // Nothing
            } else {
                $clock_hits = json_decode(Storage::get("wsa_metrics/$att->id/clock_hits"), true);
                $hits += $clock_hits[$q - 1];
            }
        }

        $topics = [];
        foreach ($data['topics'] as $t_id) {
            $topic = TagsModel::where("id", $t_id)->first();

            $topics[] = [
                "id" => $topic->id,
                "name" => $topic->name,
            ];
        };

        return [
            "correct" => round($correct / count($all_attempts) * 100, 3),
            "left" => round($left / count($all_attempts) * 100, 3),
            "hits" => round($hits / count($all_attempts), 3),
            "topics" => $topics,
        ];
    }

    public function ws_q_user_details($wsid, $qno, $username)
    {
        /**
         * Get details of the particular user's attempt at the particular Question.
         * 
         * To Return:
         * 1. If the user got it right/wrong/left it
         * 2. % of users the user answered faster than
         * 3. (LATER) data compared to other questions of the same topic
         * 
         */

        $ret = [];

        $attemptee = UserModel::where("username", $username)->first();

        $ws = WorksheetModel::where('id', $wsid)->first();
        $ws_info = json_decode(Storage::get("WS/$ws->ws_name"), true);
        $q = $ws_info['content'][$qno - 1];

        $attempt = wsAttemptsModel::where("wsid", $ws->id)
            ->where("attemptee", $attemptee->id)
            ->first();

        //$results = json_decode($attempt->results);
        $results = $attempt->results();

        switch ($results[$qno - 1]) {
            case 'T':
                $ret['qstatus'] = "@$username got the question right";
                break;

            case 'F':
                $ret['qstatus'] = "@$username got the question wrong";
                break;

            case 'L':
                $ret['qstatus'] = "@$username left the question";
                break;

            default:
                # code...
                break;
        }

        $self_clock_hits = json_decode(Storage::get("wsa_metrics/$attempt->id/clock_hits"), true)[$qno - 1];
        $ws_all_attempts = wsAttemptsModel::where("wsid", $ws->id)->get();

        $hits_lower = 0;
        foreach ($ws_all_attempts as $att) {
            $clock_hits = json_decode(Storage::get("wsa_metrics/$att->id/clock_hits"), true);

            if ($att->attemptee == $attemptee->id) {
                // Do nothing
            } else {
                // Not the current user
                $current_hit = $clock_hits[$qno - 1];

                if ($current_hit < $self_clock_hits) {
                    $hits_lower++;
                }
            }
        }

        $ret['hits_lower'] = round(($hits_lower / count($ws_all_attempts)) * 100, 3);


        return $ret;
    }
}
