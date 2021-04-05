<?php

namespace App;

use App\utils\randstring;
use App\KeysModel;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'provider_name', 'provider_id', 'password', 'remember_token',
    ];

    public function isPremium()
    {
        $payments = PaymentsModel::where("from", $this->username)
            ->where("created_at", ">", Carbon::now()
                ->subDays(30)
                ->toDateTimeString())
            ->first();

        if ($payments == null) {
            // The user is not a premium Member
            return false;
        } else {
            // The user is a premium Member
            return true;
        }
    }

    public function premium_expdate()
    {
        $payments = PaymentsModel::where("from", $this->username)
            ->where("created_at", ">", Carbon::now()
                ->subDays(30)
                ->toDateTimeString())
            ->first();

        return $payments->created_at->addDays(30)->diffForHumans();
    }

    public function generateToken()
    {
        //$this->api_token = str_random(60);
        //$this->save();

        /**
         * generate a new token and return it
         */
        $key = new KeysModel;
        $key->for = $this->username;
        $key->apikey = randstring::generate(32);
        $key->save();

        $this->api_token = $key->apikey;
        $this->save();

        return $key->apikey;
    }

    public function isAdmin()
    {
        if($this->type == "admin"){
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
        if($this->type == "student"){
            return true;
        } else {
            return false;
        }
    }
}
