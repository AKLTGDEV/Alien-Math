<?php

namespace App\Http\Controllers;

use App\activitylog;
use App\profile\actilog;
use App\SAQ;
use App\SQA;
use App\tags;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class SQAController extends Controller
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
         * Create a new SQA
         * 
         */

        return view("sqa.new", [
            "searchbar" => false,
            "topics" => tags::top20(),
        ]);
    }

    public function new_submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'body' => ['required', 'string'],
            'explanation' => ['required', 'string'],
            'O1' => ['required', 'string'],
            'O2' => ['required', 'string'],
            'O3' => ['required', 'string'],
            'O4' => ['required', 'string'],

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

        SQA::new($data);

        return Redirect::to(route('namedprofile', [Auth::user()->username]))->with([
            "status" => "success",
            "message" => "SQA Posted",
        ]);
    }
}
