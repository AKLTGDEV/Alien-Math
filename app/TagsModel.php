<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TagsModel extends Model
{
    protected $table = 'tags';

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            Storage::put("tags/" . md5($model->name), "[]");
        });

        self::created(function ($model) {
            // ... code here
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
