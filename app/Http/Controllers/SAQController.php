<?php

namespace App\Http\Controllers;

use App\activitylog;
use App\profile\actilog;
use App\SAQ;
use App\tags;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class SAQController extends Controller
{
    public function __construct()
    {
        $this->middleware([
            "web",
            "auth"
        ]);
    }

    public function new()
    {
        /**
         * Create a new SAQ
         * 
         */

        return view("saq.new", [
            "searchbar" => false,
            "topics" => tags::top20(),
        ]);
    }

    public function new_submit(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'body' => ['required', 'string'],
            'explanation' => ['required', 'string'],
            'correct' => ['required', 'string',],
            'topics'  => ['required'],

            'grade' => ['required', 'string'],
            'difficulty' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        //$digest = new Html2Text($data['body']);
        //$digest = $digest->getText();
        $digest = $data['body'];
        $digest = str_replace("_", " ", $digest);
        $digest = strtolower($digest);

        //Create the Question
        SAQ::new($data);

        return Redirect::to(route('namedprofile', [Auth::user()->username]))->with([
            "status" => "success",
            "message" => "SAQ Posted",
        ]);
    }
}
