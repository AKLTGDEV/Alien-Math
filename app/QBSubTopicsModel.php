<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QBSubTopicsModel extends Model
{
    protected $table = 'qb_subtopics';

    public function __construct()
    {
        // Do nothing
    }

    public function generic($author, $name, $parentid)
    {
        $this->author = $author;
        $this->name = $name;
        $this->parent = $parentid;

        $this->save();
    }

    public function addpost($postid)
    {
        $list = json_decode($this->list, true);
        array_push($list, $postid);
        $this->list = json_encode($list);

        $this->posts++;
        $this->save();
    }
}
