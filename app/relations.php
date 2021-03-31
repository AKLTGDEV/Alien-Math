<?php

namespace App;

use App\numbersT;

class relations
{
    static public function follow($self_uname, $uname)
    {
        $self = UserModel::where('username', '=', $self_uname)->first();

        // Check if the concerned user even exists or not
        $user = UserModel::where('username', '=', $uname)->first();
        if ($user === null) {
            // user doesn't exist
            return abort(404);
        }

        //The user exists. Check if it is the current user.
        if ($self->username == $uname) { //Following yourself?
            return "F";
        } else {
            $self_following = json_decode($self->following);
            $user_followers = json_decode($user->followers);

            //Check if self is already following user or not
            if (in_array($user->id, $self_following)) {
                return "F";
            }


            // Add user to self's following
            array_push($self_following, $user->id);
            $self->following = json_encode($self_following);
            $self->nos_following++;
            $self->save();

            // Add self to user's followers
            array_push($user_followers, $self->id);
            $user->followers = json_encode($user_followers);
            $user->nos_followers++;
            $user->save();

            rating::update($user->username);

            return "S";
        }
    }

    static public function unfollow($self_uname, $uname)
    {
        $self = UserModel::where('username', '=', $self_uname)->first();

        // Check if the concerned user even exists or not
        $user = UserModel::where('username', '=', $uname)->first();
        if ($user === null) {
            // user doesn't exist
            return abort(404);
        }

        //The user exists. Check if it is the current user.
        if ($self->username == $uname) { //unFollowing yourself?
            return "F";
        } else {
            $self_following = json_decode($self->following);
            $user_followers = json_decode($user->followers);

            if (!in_array($user->id, $self_following)) {
                //Nothing to do here.
                return "F";
            }


            // Remove user from self's following
            array_splice($self_following, array_search($user->id, $self_following));
            $self->following = json_encode($self_following);
            $self->nos_following--;
            $self->save();

            // Remove self from user's followers
            array_splice($user_followers, array_search($self->id, $user_followers));
            $user->followers = json_encode($user_followers);
            $user->nos_followers--;
            $user->save();

            rating::update($user->username);

            return "S";
        }
    }
}
