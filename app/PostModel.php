<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostModel extends Model
{
    protected $table = 'posts';

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            //
        });

        self::created(function ($model) {
            // UPDATE AUTHOR'S NEWSFEED
        });

        self::updating(function ($model) {
            // ... code here
        });

        self::updated(function ($model) {
            // ... code here
        });

        self::deleting(function ($model) {
            // ... code here
        });

        self::deleted(function ($model) {
            // ... code here
        });
    }
}
