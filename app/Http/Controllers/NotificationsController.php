<?php

namespace App\Http\Controllers;

use App\ClassroomModel;
use App\notifications;
use App\NotifsModel;
use App\PostModel;
use App\WorksheetModel;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Request;

class NotificationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $notifs = NotifsModel::where('for', '=', Auth::user()->id)->orderBy('created_at', 'desc')->get();
        return view("notifications", [
            "notifications" => $notifs,
            "searchbar" => true,
        ]);
    }
    public function view($id)
    {
        $notif = NotifsModel::where('id', '=', $id)->first();
        /**
         * Check if the notification belongs to this user or not
         * FIXME
         */

        if ($notif->type == 1) {
            // --Post--
            /**
             * Someone the user follows has posted a question
             */
            $notif->seen = 1;
            $post = PostModel::where('id', '=', $notif->postid)->first();
            $notif->save();
            return redirect()->route('viewpost', [$post->id]);
        }
        if ($notif->type == 2) {
            // --WS--
            /**
             * Someone the user follows has posted a WS
             */
            $notif->seen = 1;
            $ws = WorksheetModel::where('id', '=', $notif->postid)->first();
            $notif->save();
            return redirect()->route('wsanswer-2', [$ws->id]);
        }
        if ($notif->type == 3) {
            // --WS INVITE--
            /**
             * The user has been invited by someone to attempt a Worksheet
             */
            $notif->seen = 1;
            $ws = WorksheetModel::where('id', '=', $notif->postid)->first();
            $notif->save();
            return redirect()->route('wsanswer-2', [$ws->id]);
        }
        if ($notif->type == 4) {
            // --CLS INVITE--
            /**
             * The user has been invited by someone to join a classroom
             */
            $notif->seen = 1;
            $class = ClassroomModel::where('id', '=', $notif->postid)->first();
            $notif->save();
            return redirect()->route('viewclassroom', [$class->id]);
        }
    }

    public function list_api(Request $request)
    {
        // List notifications of current user

        $idx = json_decode($request->idx, true);
        $content = notifications::get_content(Auth::user(), $idx);

        return $content;

        /*return [
            'result' => $content['result'],
            'idx' => $content['idx'],
        ];*/
    }
}
