<?php

namespace App;

use App\utils\randstring;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class QBTestsModel extends Model
{
    protected $table = 'qb_tests';

    public function __construct()
    {
        // Do nothing
    }

    public function make($author, $title, $topic, $subtopic)
    {
        /**
         * Return the encname
         */

        $this->author = $author;
        $this->title = $title;
        $this->topic = $topic;
        $this->subtopic = $subtopic;

        $encname = randstring::generate(32);
        while (true) {
            $prev = QBTestsModel::where("encname", $encname)->get();
            if (count($prev) == 0) {
                break;
            }

            $encname = randstring::generate(32);
        }

        $this->encname = $encname;
        $this->save();

        return $encname;
    }

    public function content()
    {
        $content = json_decode(Storage::get("QB_tests/$this->encname"), true);

        $topic = QBTopicsModel::where("id", $this->topic)->first();
        $subtopic = QBSubTopicsModel::where("id", $this->subtopic)->first();

        //return $content;

        $qnos = 0;
        foreach ($content as $key => $value) {
            $pattern = "/marks/i";
            if (preg_match($pattern, $key)) {
                $qnos++;
            }
        }

        $ret = array(
            'itemT' => 'BANKtest',
            'id' => $this->id,
            'content' => $content,
            "qnos" => $qnos,
            'topic' => [
                "id" => $topic->id,
                "name" => $topic->name
            ],
            'subtopic' => [
                "id" => $subtopic->id,
                "name" => $subtopic->name
            ],
        );

        return $ret;
    }
}
