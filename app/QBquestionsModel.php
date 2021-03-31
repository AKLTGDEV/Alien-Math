<?php

namespace App;

use App\utils\randstring;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class QBquestionsModel extends Model
{
    protected $table = 'qb_questions';

    public function __construct()
    {
        // Do nothing
    }

    public function mcq($author, $title, $topic, $subtopic)
    {
        /**
         * Return the encname
         */

        $this->type = 1; // MCQ
        $this->author = $author;
        $this->title = $title;
        $this->topic = $topic;
        $this->subtopic = $subtopic;

        $encname = randstring::generate(32);
        while (true) {
            $prev = QBquestionsModel::where("encname", $encname)->get();
            if (count($prev) == 0) {
                break;
            }

            $encname = randstring::generate(32);
        }

        $this->encname = $encname;
        $this->save();

        return $encname;
    }

    public function subjective($author, $title, $topic, $subtopic)
    {
        /**
         * Return the encname
         */

        $this->type = 2; // Subjective
        $this->author = $author;
        $this->title = $title;
        $this->topic = $topic;
        $this->subtopic = $subtopic;

        $encname = randstring::generate(32);
        while (true) {
            $prev = QBquestionsModel::where("encname", $encname)->get();
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
        $content = json_decode(Storage::get("QB_questions/$this->encname"), true);

        $topic = QBTopicsModel::where("id", $this->topic)->first();
        $subtopic = QBSubTopicsModel::where("id", $this->subtopic)->first();

        if ($this->type == 1) { // MCQ
            $options = [];
            for ($i = 1; $i <= $content['opt_nos']; $i++) {
                array_push($options, $content["option$i"]);
            }

            return array(
                'itemT' => 'BANKpost',
                'pid' => $this->id,
                'title' => $content['title'],
                'body' => $content['Qbody'],
                'opt_nos' => $content['opt_nos'],
                'options' => $options,
                'correctopt' => $content['correct'],
                'topic' => [
                    "id" => $topic->id,
                    "name" => $topic->name
                ],
                'subtopic' => [
                    "id" => $subtopic->id,
                    "name" => $subtopic->name
                ],
            );
        } else if ($this->type == 2) { //Subjective
            return array(
                'itemT' => 'BANKSubjective',
                'pid' => $this->id,
                'title' => $content['title'],
                'body' => $content['Qbody'],
                'topic' => [
                    "id" => $topic->id,
                    "name" => $topic->name
                ],
                'subtopic' => [
                    "id" => $subtopic->id,
                    "name" => $subtopic->name
                ],
            );
        }
    }
}
