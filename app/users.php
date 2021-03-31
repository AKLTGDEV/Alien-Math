<?php

namespace App;

use App\UserModel;
use Illuminate\Support\Facades\Storage;
use App\TagsModel;

class users
{
    public static function exists($id)
    {
        $u = UserModel::where('id', $id)->first();
        if ($u != null) {
            return true;
        } else {
            return false;
        }
    }

    public static function exists_uname($uname)
    {
        $u = UserModel::where('username', $uname)->first();
        if ($u != null) {
            return true;
        } else {
            return false;
        }
    }

    public static function getid_by_uname($uname)
    {
        $u = UserModel::where('username', $uname)->first();
        if ($u != null) {
            return $u->id;
        } else {
            return 0;
        }
    }

    public static function getbio($uname)
    {
        if (Storage::disk('local')->exists("bio/{$uname}") == false) {
            //Just making sure
            Storage::put("bio/" . $uname, "");
        }

        return Storage::get("bio/" . $uname);
    }

    public static function storebio($uname, $bio)
    {
        Storage::put("bio/" . $uname, $bio);
    }

    public static function storetags($uname, $tags)
    {
        $tags_new = array();
        foreach ($tags as $tag) {
            $tag = trim($tag);
            $tag_entry = TagsModel::where('name', $tag)->first();
            array_push($tags_new, $tag_entry->name);
        }
        $tags = $tags_new;

        Storage::put("user_tags/" . $uname, json_encode($tags, true));
    }

    public static function gettags($uname)
    {
        /*$user = UserModel::where('username', $uname)->first();
        return $user->tags;*/

        if (Storage::disk('local')->exists("user_tags/{$uname}") == false) {
            //Just making sure
            Storage::put("user_tags/" . $uname, "[]");
            return "[]";
        }

        return Storage::get("user_tags/" . $uname);
    }

    public static function get_ext($uname)
    {
        $User = UserModel::where('username', $uname)->first();
        if (Storage::exists("user_ext/".$uname) == false) {
            //Just making sure

            if (count(explode(" ", $User->name)) < 2) {
                $fname = $User->name;
                $lname = null;
            } else {
                $fname = explode(" ", $User->name)[0];
                $lname = explode(" ", $User->name)[1];
            }

            $user_ext_data = [
                "fname" => $fname,
                "lname" => $lname,
                "address" => null,
                "phone" => null,
                "occupation" => null,
            ];
            Storage::put("user_ext/" . $uname, json_encode($user_ext_data));

            return $user_ext_data;
        } else {
            return json_decode(Storage::get("user_ext/" . $uname), true);
        }
    }
}
