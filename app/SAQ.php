<?php

namespace App;

//use Html2Text\Html2Text;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use TeamTNT\TNTSearch\TNTSearch;

class SAQ extends Model
{
    protected $table = 'SAQ';

    public static function Table()
    {
        return (new self())->getTable();
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
        return Storage::disk('local')->get("saq/body/$this->id");
    }

    public function GetExplanation()
    {
        return Storage::disk('local')->get("saq/explanation/$this->id");
    }

    public static function new($data)
    {
        //$digest = new Html2Text($data['body']);
        //$digest = $digest->getText();
        $digest = $data['body'];
        $digest = str_replace("_", " ", $digest);
        $digest = strtolower($digest);

        //Create the Question
        $q = new SAQ;
        $q->correct = $data['correct'];
        $q->type = $data['grade'];
        $q->difficulty = $data['difficulty'];
        $q->topics = $data['topics'];
        $q->digest = $digest;
        $q->uploader = Auth::user()->username;
        $q->save();

        $q->SaveBody($data['body']);
        $q->SaveExplanation($data['explanation']);
        $q->updateindex();
    }

    public function data_update($data)
    {
        //$digest = new Html2Text($data['body']);
        //$digest = $digest->getText();
        $digest = $data['body'];
        $digest = str_replace("_", " ", $digest);
        $digest = strtolower($digest);

        //Create the Question
        $this->correct = $data['correct'];
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

        $tnt->selectIndex("saq.index");
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
        Storage::disk('local')->put("saq/body/$id", $body);
    }

    public function SaveExplanation($explanation)
    {
        // Save Explanation to local storage
        $id = $this->id;
        Storage::disk('local')->put("saq/explanation/$id", $explanation);
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

        $tnt->selectIndex("saq.index");
        $index = $tnt->getIndex();

        $index->insert([
            'id' => $this->id,
            'digest' => $this->digest,
        ]);
    }
}
