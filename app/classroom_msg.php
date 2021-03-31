<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class classroom_msg extends Model
{
    protected $fillable = [
        'msg'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
