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
use League\Csv\Reader;

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

        $q = SQA::new($data);

        switch ($request->submit_mode) {
            case 1:
                return redirect()
                    ->route('question.SQA', [
                        $q->id
                    ]);
                break;

            case 2:
                return redirect()
                    ->route('q.gateway.add');
                break;

            default:
                return abort(501);
                break;
        }

        /*return Redirect::to(route('namedprofile', [Auth::user()->username]))->with([
            "status" => "success",
            "message" => "SQA Posted",
        ]);*/
    }

    public function upload()
    {
        return view("sqa.upload", [
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
                SQA::new([
                    "body" => $record['question'],
                    "O1" => $record['O1'],
                    "O2" => $record['O2'],
                    "O3" => $record['O3'],
                    "O4" => $record['O4'],
                    "grade" => $record['grade'],
                    "difficulty" => $record['difficulty'],
                    "topics" => $record['tags'],
                    "explanation" => $record['explanation']
                ]);

                $count++;
            }

            return Redirect::to(route('namedprofile', [$author->username]))->with([
                "status" => "success",
                "message" => $count . " SQAs Uploaded",
            ]);
        }
    }

    public function edit(Request $request)
    {
        $q = SQA::where("id", $request->id)->first();
        if ($q != null) {
            return view('sqa.edit', [
                "topics" => tags::top20(),
                "question" => $q,
            ]);
        } else {
            return abort(404);
        }
    }

    public function edit_submit(Request $request, $id)
    {
        $q = SQA::where("id", $request->id)->first();
        if ($q != null) {
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

            $q->data_update($data);

            return redirect()->route("namedprofile", [Auth::user()->username]);
        } else {
            return abort(404);
        }
    }
}
