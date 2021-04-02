<?php

namespace App\Http\Controllers;

use App\PostModel;
use App\posts;
use App\SAQ;
use App\SQA;
use App\TagsModel;
use App\UserModel;
use App\WorksheetModel;
use App\worksheets;
use Symfony\Component\HttpFoundation\Request;
use TeamTNT\TNTSearch\TNTSearch;

class SearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $query = $request->q;

        /**
         * Search in the DB for 4 types of items: Users, 
         * topics, posts, and worlsheets.
         */

        $tnt = new TNTSearch;

        $tnt->loadConfig([
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', '127.0.0.1'),
            'database'  => env('DB_DATABASE', 'forge'),
            'username'  => env('DB_USERNAME', 'forge'),
            'password'  => env('DB_PASSWORD', ''),
            'storage'   => storage_path('app') . "/indices//",
        ]);

        $interim_array = [];

        /**
         * Search for the posts first
         */
        $tnt->selectIndex("posts.index");
        $res = $tnt->search($query, 20);
        foreach ($res['ids'] as $resid) {
            $P = PostModel::where('id', $resid)->first();
            $interim_array['P::' . $resid] = $P->updated_at;
        }

        /**
         * Search for the Worksheets
         */
        $tnt->selectIndex("ws.index");
        $res = $tnt->search($query, 20);
        foreach ($res['ids'] as $resid) {
            $WS = WorksheetModel::where('id', $resid)->first();
            $interim_array['W::' . $resid] = $WS->updated_at;
        }

        /**
         * Search for the Users
         */
        $tnt->selectIndex("users.index");
        $res = $tnt->search($query, 20);
        foreach ($res['ids'] as $resid) {
            $u = UserModel::where("id", $resid)->first();
            $interim_array['U::' . $resid] = $u->updated_at;
        }

        /**
         * Search for the tags
         */
        $tnt->selectIndex("tags.index");
        $res = $tnt->search($query, 20);
        foreach ($res['ids'] as $resid) {
            $t = TagsModel::where("id", $resid)->first();
            $interim_array['T::' . $resid] = $t->updated_at;
        }

        array_multisort($interim_array);
        $interim_array = array_reverse($interim_array);
        //dd($interim_array);

        $final_array = [];

        foreach ($interim_array as $interim_el => $mod_date) {
            $element = explode("::", $interim_el);
            switch ($element[0]) { // Find out what this is.
                case 'P':
                    array_push($final_array, [
                        "type" => "POST",
                        "body" => posts::get($element[1]),
                        "samay" => $mod_date,
                    ]);
                    break;

                case 'W':
                    array_push($final_array, [
                        "type" => "WS",
                        "body" => worksheets::get($element[1]),
                        "samay" => $mod_date,
                    ]);
                    break;

                case 'U':
                    array_push($final_array, [
                        "type" => "USER",
                        "body" => UserModel::where('id', $element[1])->first(),
                        "samay" => $mod_date,
                    ]);
                    break;

                case 'T':
                    array_push($final_array, [
                        "type" => "TAG",
                        "body" => TagsModel::where("id", $element[1])->first(),
                        "samay" => $mod_date,
                    ]);
                    break;

                default:
                    return abort(500);
                    break;
            }
        }

        //dd($final_array);


        //dd($search_res);

        return view("search", [
            "searchbar" => true,
            "query" => $query,
            "results" => $final_array
        ]);
    }

    public function question_search(Request $request)
    {
        $search = $request->q;

        $hits = 0;
        $exec_time = 0;
        $results = [];

        $tnt = new TNTSearch;

        $tnt->loadConfig([
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('DB_DATABASE', ''),
            'username'  => env('DB_USERNAME', ''),
            'password'  => env('DB_PASSWORD', ''),
            'storage'   => storage_path('app') . "/indices//",
        ]);

        $tnt->selectIndex("posts.index");
        $res = $tnt->search($search);
        $exec_time += explode(" ", $res['execution_time'])[0];
        $hits += $res['hits'];
        foreach ($res['ids'] as $id) {
            //$results[] = MCQ::where("id", $id)->first();
            $results[] = PostModel::where("id", $id)->first();
        }

        $tnt->selectIndex("saq.index");
        $res = $tnt->search($search);
        $exec_time += explode(" ", $res['execution_time'])[0];
        $hits += $res['hits'];
        foreach ($res['ids'] as $id) {
            $results[] = SAQ::where("id", $id)->first();
        }

        $tnt->selectIndex("sqa.index");
        $res = $tnt->search($search);
        $exec_time += explode(" ", $res['execution_time'])[0];
        $hits += $res['hits'];
        foreach ($res['ids'] as $id) {
            $results[] = SQA::where("id", $id)->first();
        }


        /**
         * 
         * Check if the gathered results have the filters applicable
         * 
         */
        /*$final = [];

        foreach ($results as $r) {
            $grade_flag = false;
            $difficulty_flag = false;
            $topics_flag = false;
            //Only when all 3 flags are set, would the element show up

            if ($request->grade != "X") {
                if ($r->type == $request->grade) {
                    $grade_flag = true;
                } else {
                    continue;
                }
            } else {
                $grade_flag = true;
            }

            if ($request->difficulty != "X") {
                if ($r->difficulty == $request->difficulty) {
                    $difficulty_flag = true;
                } else {
                    continue;
                }
            } else {
                $difficulty_flag = true;
            }

            if ($request->topics != null) {
                foreach (explode(",", $request->topics) as $t) {
                    if ($r->hasTopic($t)) {
                        $topics_flag = true;
                    }
                }
            } else {
                $topics_flag = true;
            }

            if ($grade_flag && $difficulty_flag && $topics_flag) {
                $final[] = $r;
            }
        }*/

        return view("qsearchresults", [
            "results" => $results,
            "exec_time" => round($exec_time, 3),
            "hits" => count($results),
        ]);

        /*return view("search", [
            "results" => $final,
            "exec_time" => round($exec_time, 3),
            "hits" => count($final),
        ]);*/
    }
}
