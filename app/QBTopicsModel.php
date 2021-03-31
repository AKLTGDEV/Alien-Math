<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QBTopicsModel extends Model
{
    protected $table = 'qb_topics';

    public function __construct()
    {
        // Do nothing
    }

    public function generic($author, $name)
    {
        $this->author = $author;
        $this->name = $name;

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
