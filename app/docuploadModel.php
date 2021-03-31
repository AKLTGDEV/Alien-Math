<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class docuploadModel extends Model
{
    protected $table = 'docuploads';

    public static function boot()
    {
        parent::boot();
    }
}
