<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PostModel extends Model
{
    protected $table = 'posts';

    public static function Table()
    {
        //return (new self())->getTable();
        return "MCQ";
    }

    public function info()
    {
        //$topics__ = explode(",", $this->tags);
        $topics__ = json_decode($this->tags);
        $topics = [];

        foreach ($topics__ as $t) {
            $topics[] = TagsModel::where("name", $t)->first()->id;
        }

        return [
            "type" => "MCQ",
            "id" => $this->id,
            "body" => $this->getBody(),
            "opts" => json_decode($this->opts),
            "correct" => $this->correctopt,
            "explanation" => $this->GetExplanation(),
            "topics" => $topics,
        ];
    }

    public function getBody()
    {
        $post_body = posts::getbody($this->text);

        $img_present = false;
        if (strpos($post_body, '<img style=') !== false) {
            $img_present = true;
        }

        // Normalize the images
        $body_new = str_replace('<img style=', '<img id="postimg-' . $this->id . '" class="img-fluid" style=', $post_body);

        return $body_new;
    }

    public function getExplanation()
    {
        return Storage::disk('local')->get("posts/explanation/$this->id");
    }

    public function SaveExplanation($explanation)
    {
        // Save Explanation to local storage
        $id = $this->id;
        Storage::disk('local')->put("posts/explanation/$id", $explanation);
    }

    public function getTopics()
    {
        return json_decode($this->tags);
    }

    public function uploader()
    {
        return UserModel::where("id", $this->author)->first()->username;
    }

    public function addVideo($id)
    {
        $videos = json_decode($this->videos);
        if (!in_array($id, $videos)) {
            $videos[] = $id;
            $this->videos = json_encode($videos);
            $this->save();
        }
    }

    public function deleteVideo($id)
    {
        $videos = json_decode($this->videos);
        $videos_new = [];
        foreach ($videos as $v) {
            if ($v != $id) {
                $videos_new[] = $v;
            }
        }
        $this->videos = json_encode($videos_new);
        $this->save();
    }
}
