<?php

namespace App;

class adminlist {

    public static function isadmin($username) {

        $list = array(
            "admin",
        );

        if(in_array($username, $list)){
            return true;
        } else {
            return false;
        }
    }
}

?>