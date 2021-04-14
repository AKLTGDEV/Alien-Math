<?php

namespace App\Http\Controllers;

use App\PostModel;
use App\SAQ;
use App\SQA;
use App\UserModel;
use App\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use TeamTNT\TNTSearch\TNTSearch;

class VideoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function upload(Request $request)
    {
        $vid = $request->file('video');

        $v = new Video;
        $v->filename = $vid->getClientOriginalName();

        $v->searchterm = pathinfo($v->filename, PATHINFO_FILENAME);
        $v->searchterm = str_replace("-", " ", $v->searchterm);
        $v->searchterm = str_replace("_", " ", $v->searchterm);
        //return $v->searchterm;

        $v->encname = md5(
            Carbon::now()->toDayDateTimeString()
                . $v->filename
                . Auth::user()->username
                . rand(
                    0,
                    69
                )
        );

        if (Storage::putFileAs("videos", $vid, $v->encname)) {
            $v->uploader = Auth::user()->username;
            $v->save();

            switch ($request->qtype) {
                case 'MCQ':
                    PostModel::where("id", $request->qid)
                        ->first()
                        ->addVideo($v->id);
                    $v->attached++;
                    break;

                case 'SAQ':
                    SAQ::where("id", $request->qid)
                        ->first()
                        ->addVideo($v->id);
                    $v->attached++;
                    break;

                case 'SQA':
                    SQA::where("id", $request->qid)
                        ->first()
                        ->addVideo($v->id);
                    $v->attached++;
                    break;

                default:
                    //dd("Somethng broke");
                    break;
            }

            $v->save();

            $tnt = new TNTSearch;
            $tnt->loadConfig([
                'driver'    => 'mysql',
                'host'      => env('DB_HOST', '127.0.0.1'),
                'database'  => env('DB_DATABASE', 'forge'),
                'username'  => env('DB_USERNAME', 'forge'),
                'password'  => env('DB_PASSWORD', ''),
                'storage'   => storage_path('app') . "/indices//",
            ]);
            $tnt->selectIndex("videos.index");
            $index = $tnt->getIndex();

            $index->insert([
                'id' => $v->id,
                'searchterm' => $v->searchterm
            ]); // Not Now..
        }

        return redirect()->back();
    }

    public function stream($id)
    {
        $v = Video::where("id", $id)->first();
        if ($v == null) {
            return abort(404);
        } else {
            $path = Storage::path("videos/$v->encname");
            $mime_type = mime_content_type($path);

            return response()->file($path, [
                'Content-Type' => $mime_type,
                'Content-Disposition' => 'inline; filename="video"'
            ]);
        }
    }

    public function modify($id)
    {
        $v = Video::where("id", $id)
            ->first();
        if ($v == null) {
            return abort(404);
        } else {
            /**
             * Emit all the attatched posts
             * 
             */

            $posts = [];

            foreach (json_decode($v->MCQ) as $mcq) {
                $posts[] = [
                    "type" => "MCQ",
                    "id" => $mcq,
                    "url" => route('question.MCQ', [$mcq]),
                    "detach" => route('video.detach.MCQ', [$v->id, $mcq]),
                ];
            }

            foreach (json_decode($v->SAQ) as $saq) {
                $posts[] = [
                    "type" => "SAQ",
                    "id" => $saq,
                    "url" => route('question.SAQ', [$saq]),
                    "detach" => route('video.detach.SAQ', [$v->id, $saq]),
                ];
            }

            foreach (json_decode($v->SQA) as $sqa) {
                $posts[] = [
                    "type" => "SQA",
                    "id" => $sqa,
                    "url" => route('question.SQA', [$sqa]),
                    "detach" => route('video.detach.SQA', [$v->id, $sqa]),
                ];
            }

            return view('video.modify', [
                "video" => $v,
                "posts" => $posts,
            ]);
        }
    }

    public function detach_mcq($id, $qid)
    {
        $v = Video::where("id", $id)
            ->first();
        if ($v == null) {
            return abort(404);
        } else {
            $q = PostModel::where("id", $qid)->first();
            $q->deleteVideo($id);

            return redirect()->back();
        }
    }

    public function detach_saq($id, $qid)
    {
        $v = Video::where("id", $id)
            ->first();
        if ($v == null) {
            return abort(404);
        } else {
            $q = SAQ::where("id", $qid)->first();
            $q->deleteVideo($id);

            return redirect()->back();
        }
    }

    public function detach_sqa($id, $qid)
    {
        $v = Video::where("id", $id)
            ->first();
        if ($v == null) {
            return abort(404);
        } else {
            $q = SQA::where("id", $qid)->first();
            $q->deleteVideo($id);

            return redirect()->back();
        }
    }

    public function attach(Request $request, $id)
    {
        $v = Video::where("id", $id)
            ->first();
        if ($v == null) {
            return abort(404);
        } else {
            switch ($request->qtype) {
                case 'MCQ':
                    $q = PostModel::where("id", $request->qid)->first();
                    break;

                case 'SAQ':
                    $q = SAQ::where("id", $request->qid)->first();
                    break;

                case 'SQA':
                    $q = SQA::where("id", $request->qid)->first();
                    break;

                default:
                    dd("something went wrong");
                    break;
            }

            $q->addVideo($id);
            $v->attached++;
            $v->save();

            return redirect()->back();
        }
    }

    public function search(Request $request)
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

        $tnt->selectIndex("videos.index");
        $res = $tnt->search($search);
        $exec_time += explode(" ", $res['execution_time'])[0];
        $hits += $res['hits'];
        foreach ($res['ids'] as $id) {
            $results[] = Video::where("id", $id)->first();
        }

        return view("video.search", [
            "results" => $results,
            "exec_time" => round($exec_time, 3),
            "hits" => count($results),
        ]);
    }

    public function delete($id)
    {
        // Remove the file from local storage
        $v = Video::where("id", $id)
            ->first();
        if ($v == null) {
            return abort(404);
        } else {
            Storage::delete("videos/$v->encname");

            // Remove the DB entry
            $v->delete();
            return redirect()->route('stats');
        }
    }

    public function all()
    {
        $videos = Video::where("uploader", Auth::user()->username)
            ->orderBy('id', 'desc')
            ->get(); // Get all videos
        return view("video.all", [
            "videos" => $videos,
        ]);
    }

    public function bookmark_mcq($qid)
    {
        $user = Auth::user();
        if ($user != null) {
            if (PostModel::where("id", $qid)->first() != null) {
                $user->bookmark_mcq($qid);
                return [
                    "status" => "ok"
                ];
            }
        }

        return [
            "status" => "error"
        ];
    }

    public function bookmark_saq($qid)
    {
        $user = Auth::user();
        if ($user != null) {
            if (SAQ::where("id", $qid)->first() != null) {
                $user->bookmark_saq($qid);
                return [
                    "status" => "ok"
                ];
            }
        }

        return [
            "status" => "error"
        ];
    }

    public function bookmark_sqa($qid)
    {
        $user = Auth::user();
        if ($user != null) {
            if (SQA::where("id", $qid)->first() != null) {
                $user->bookmark_sqa($qid);
                return [
                    "status" => "ok"
                ];
            }
        }

        return [
            "status" => "error"
        ];
    }

    public function bookmarked()
    {
        $vid_ids = array_unique(array_merge(
            json_decode(Auth::user()->vid_MCQ, true),
            json_decode(Auth::user()->vid_SAQ, true),
            json_decode(Auth::user()->vid_SQA, true),
        ));

        $videos = [];

        foreach ($vid_ids as $vid) {
            $videos[] = Video::where("id", $vid)
                ->first();
        }
        return view("video.bookmarked", [
            "videos" => $videos,
        ]);
    }
}
