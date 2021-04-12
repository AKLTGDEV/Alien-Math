<?php

namespace App\Http\Controllers;

use App\PostModel;
use App\SAQ;
use App\SQA;
use App\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

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
                    break;

                case 'SAQ':
                    SAQ::where("id", $request->qid)
                        ->first()
                        ->addVideo($v->id);
                    break;

                case 'SQA':
                    SQA::where("id", $request->qid)
                        ->first()
                        ->addVideo($v->id);
                    break;

                default:
                    dd("Somethng broke");
                    break;
            }

            $v->attached++;
            $v->save();
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
}
