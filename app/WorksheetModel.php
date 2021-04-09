<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class WorksheetModel extends Model
{
    protected $table = 'worksheets';

    public function topics()
    {
        $ws_info = json_decode(Storage::get("WS/$this->ws_name"), true);
        return array_values($ws_info['tags']);
    }
}