<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class wsAttemptsModel extends Model
{
    protected $table = 'ws_attempts';

    public function answer($a)
    {
        $answers = json_decode($this->answers);
        $answers[] = $a;

        $this->answers = json_encode($answers);
        $this->save();
    }

    public function clock_hit($hits)
    {
        $hits_list = json_decode(Storage::get("wsa_metrics/$this->id/clock_hits"));
        $hits_list[] = $hits;
        Storage::put("wsa_metrics/$this->id/clock_hits", json_encode($hits_list));
    }
}
