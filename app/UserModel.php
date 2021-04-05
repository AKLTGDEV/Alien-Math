<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserModel extends Model
{
    protected $table = 'users';

    public function isPremium()
    {
        $payments = PaymentsModel::where("from", $this->username)
            ->where("created_at", ">", Carbon::now()->subDays(30)->toDateTimeString())
            ->first();

        if ($payments == null) {
            // The user is not a premium Member
            return false;
        } else {
            // The user is a premium Member
            return true;
        }
    }

    public static function boot()
    {
        parent::boot();

        self::created(function ($model) {
            /**
             * Create an entry in the `user_ext` folder
             */

            if (count(explode(" ", $model->name)) < 2) {
                $fname = $model->name;
                $lname = null;
            } else {
                $fname = explode(" ", $model->name)[0];
                $lname = explode(" ", $model->name)[1];
            }

            $user_ext_data = [
                "fname" => $fname,
                "lname" => $lname,
                "address" => null,
                "phone" => null,
                "occupation" => null,
            ];

            Storage::put("user_ext/" . $model->username, json_encode($user_ext_data));

            Storage::put("answers/" . $model->username, "[]");
        });
    }

    public function isAdmin()
    {
        if ($this->type == "admin") {
            return true;
        } else {
            return false;
        }
    }

    public function isTeacher()
    {
        if($this->type == "creator"){
            return true;
        } else {
            return false;
        }
    }

    public function isStudent()
    {
        if ($this->type == "student") {
            return true;
        } else {
            return false;
        }
    }
}
