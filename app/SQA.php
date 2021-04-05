<?php

namespace App;

//use Html2Text\Html2Text;

use Html2Text\Html2Text;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use TeamTNT\TNTSearch\TNTSearch;

class SQA extends Model
{
    protected $table = 'SQA';

    public static function Table()
    {
        return (new self())->getTable();
    }

    public function info()
    {
        return [
            "type" => "SQA",
            "id" => $this->id,
            "body" => $this->GetBody(),
            "opts" => [
                $this->O1,
                $this->O2,
                $this->O3,
                $this->O4,
            ],
            "explanation" => $this->GetExplanation(),
        ];
    }

    public function firstfew($n)
    {
        return implode(' ', array_slice(explode(' ', $this->digest), 0, $n));
    }

    public function getTopics()
    {
        return explode(",", $this->topics);
    }

    function hasTopic($t)
    {
        if (in_array($t, explode(",", $this->topics))) {
            return true;
        } else {
            return false;
        }
    }

    public function GetBody()
    {
        return Storage::disk('local')->get("sqa/body/$this->id");
    }

    public function GetExplanation()
    {
        return Storage::disk('local')->get("sqa/explanation/$this->id");
    }

    public static function new($data)
    {
        $digest = new Html2Text($data['body']);
        $digest = $digest->getText();
        //$digest = $data['body'];
        $digest = str_replace("_", " ", $digest);
        $digest = strtolower($digest);

        //Create the Question
        $q = new SQA;
        $q->O1 = $data['O1'];
        $q->O2 = $data['O2'];
        $q->O3 = $data['O3'];
        $q->O4 = $data['O4'];

        $q->type = $data['grade'];
        $q->difficulty = $data['difficulty'];
        $q->topics = $data['topics'];
        $q->digest = $digest;
        $q->uploader = Auth::user()->username;
        $q->save();

        $q->SaveBody($data['body']);
        $q->SaveExplanation($data['explanation']);
        $q->updateindex();

        //Update Activity Log
        activitylog::post_sqa(Auth::user()->username, $q->id);
    }

    public function data_update($data)
    {
        $digest = new Html2Text($data['body']);
        $digest = $digest->getText();
        //$digest = $data['body'];
        $digest = str_replace("_", " ", $digest);
        $digest = strtolower($digest);

        //Create the Question
        $this->O1 = $data['O1'];
        $this->O2 = $data['O2'];
        $this->O3 = $data['O3'];
        $this->O4 = $data['O4'];

        $this->type = $data['grade'];
        $this->difficulty = $data['difficulty'];
        $this->topics = $data['topics'];
        $this->digest = $digest;
        $this->uploader = Auth::user()->username;
        $this->save();

        $this->SaveBody($data['body']);
        $this->SaveExplanation($data['explanation']);

        $tnt = new TNTSearch;
        $tnt->loadConfig([
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('DB_DATABASE', ''),
            'username'  => env('DB_USERNAME', ''),
            'password'  => env('DB_PASSWORD', ''),
            'storage'   => storage_path('app') . "/indices//",
        ]);

        $tnt->selectIndex("sqa.index");
        $index = $tnt->getIndex();

        $index->update($this->id, [
            'id' => $this->id,
            'digest' => $this->digest,
        ]);
    }

    public function SaveBody($body)
    {
        // Save body to local storage
        $id = $this->id;
        Storage::disk('local')->put("sqa/body/$id", $body);
    }

    public function SaveExplanation($explanation)
    {
        // Save Explanation to local storage
        $id = $this->id;
        Storage::disk('local')->put("sqa/explanation/$id", $explanation);
    }

    public function updateindex()
    {
        $tnt = new TNTSearch;

        $tnt->loadConfig([
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('DB_DATABASE', ''),
            'username'  => env('DB_USERNAME', ''),
            'password'  => env('DB_PASSWORD', ''),
            'storage'   => storage_path('app') . "/indices//",
        ]);

        $tnt->selectIndex("sqa.index");
        $index = $tnt->getIndex();

        $index->insert([
            'id' => $this->id,
            'digest' => $this->digest,
        ]);
    }

    public static function get($id)
    {
        $app_url = Config::get('app.url');
        $q = SQA::where("id", $id)->first();
        if ($q != null) {
            $author = UserModel::where("username", $q->uploader)->first();

            return [
                "id" => $id,
                "itemT" => "SQA",
                "body" => $q->GetBody(),
                "type" => $q->type,
                "difficulty" => $q->difficulty,
                "tags" => json_encode(explode(",", $q->topics)),
                'name' => $author->name,
                'username' => $author->username,
                'profilepic' => "{$app_url}/user/{$author->username}/profilepic",
            ];
        }

        return null;
    }

    public function uploader()
    {
        return $this->uplader;
    }
}
