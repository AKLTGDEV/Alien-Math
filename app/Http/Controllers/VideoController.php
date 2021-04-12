<?php

namespace App\Http\Controllers;

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
            $v->Qtype = $request->qtype;
            $v->Qid = $request->qid;
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
}
