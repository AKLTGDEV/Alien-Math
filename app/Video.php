<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $table = 'videos';

    public function addMCQ($id)
    {
        $list = json_decode($this->MCQ);
        if (!in_array($id, $list)) {
            $list[] = $id;

            $this->MCQ = json_encode($list);
            $this->save();
        }
    }
    public function deleteMCQ($id)
    {
        $list = json_decode($this->MCQ);
        $list_new = [];
        foreach ($list as $x) {
            if ($x != $id) {
                $list_new[] = $x;
            }
        }

        $this->MCQ = json_encode($list_new);
        $this->save();
    }


    public function addSAQ($id)
    {
        $list = json_decode($this->SAQ);
        if (!in_array($id, $list)) {
            $list[] = $id;

            $this->SAQ = json_encode($list);
            $this->save();
        }
    }
    public function deleteSAQ($id)
    {
        $list = json_decode($this->SAQ);
        $list_new = [];
        foreach ($list as $x) {
            if ($x != $id) {
                $list_new[] = $x;
            }
        }

        $this->SAQ = json_encode($list_new);
        $this->save();
    }


    public function addSQA($id)
    {
        $list = json_decode($this->SQA);
        if (!in_array($id, $list)) {
            $list[] = $id;

            $this->SQA = json_encode($list);
            $this->save();
        }
    }
    public function deleteSQA($id)
    {
        $list = json_decode($this->SQA);
        $list_new = [];
        foreach ($list as $x) {
            if ($x != $id) {
                $list_new[] = $x;
            }
        }

        $this->SQA = json_encode($list_new);
        $this->save();
    }
}
