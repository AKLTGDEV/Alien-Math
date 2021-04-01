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
use League\Csv\Reader;

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

    public function upload()
    {
        return view("saq.upload", [
            "searchbar" => false,
            "tags_suggested" => tags::top20(),
        ]);
    }

    public function upload_validate(Request $request)
    {
        $author = Auth::user();

        if ($request->file('csv')->isValid()) {
            $csv_uploaded = $request->file('csv')->path();

            //load the CSV document from a file path
            $csv = Reader::createFromPath($csv_uploaded, 'r');
            $csv->setHeaderOffset(0);

            $header = $csv->getHeader(); //returns the CSV header record
            $records = $csv->getRecords(); //returns all the CSV records as an Iterator object

            $count = 0;
            foreach ($records as $record) {

                //Each row is a seperate Question.
                SAQ::new([
                    "body" => $record['question'],
                    "correct" => $record['correct'],
                    "grade" => $record['grade'],
                    "difficulty" => $record['difficulty'],
                    "topics" => $record['tags'],
                    "explanation" => $record['explanation']
                ]);

                $count++;
            }

            return Redirect::to(route('namedprofile', [$author->username]))->with([
                "status" => "success",
                "message" => $count . " SAQs Uploaded",
            ]);
        }
    }

    public function edit(Request $request)
    {
        $q = SAQ::where("id", $request->id)->first();
        if ($q != null) {
            return view('saq.edit', [
                "topics" => tags::top20(),
                "question" => $q,
            ]);
        } else {
            return abort(404);
        }
    }

    public function edit_submit(Request $request, $id)
    {
        $q = SAQ::where("id", $request->id)->first();
        if ($q != null) {
            $validator = Validator::make($request->all(), [
                'body' => ['required', 'string'],
                'explanation' => ['required', 'string'],
                'correct' => ['required', 'string'],
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

            $q->data_update($data);

            return redirect()->route("namedprofile", [Auth::user()->username]);
        } else {
            return abort(404);
        }
    }
}
