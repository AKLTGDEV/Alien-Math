<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class wsAttemptsModel extends Model
{
    protected $table = 'ws_attempts';

    public function answer($a)
    {
        $answers = json_decode(Storage::get("wsa_metrics/$this->id/answers"));
        $answers[] = $a;

        Storage::put("wsa_metrics/$this->id/answers", json_encode($answers));
    }

    public function getanswers()
    {
        return json_decode(Storage::get("wsa_metrics/$this->id/answers"));
    }

    public function result($flag)
    {
        $results = json_decode($this->results);
        $results[] = $flag;

        $this->results = json_encode($results);
        $this->save();
    }

    public function clock_hit($hits)
    {
        $hits_list = json_decode(Storage::get("wsa_metrics/$this->id/clock_hits"));
        $hits_list[] = $hits;
        Storage::put("wsa_metrics/$this->id/clock_hits", json_encode($hits_list));
    }
}
