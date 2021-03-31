<?php

namespace App;

class groups
{
    private $internal = ["crowdoubtNEET", "crowdoubtJEE", "crowdoubtBoards"];

    public static function ismod($username)
    {
        $mods = ["admin"];

        if (in_array($username, $mods)) {
            return true;
        } else {
            return false;
        }
    }

    public static function isoperator($username)
    {
        $operators = ["admin", "sauce"];

        if (in_array($username, $operators)) {
            return true;
        } else {
            return false;
        }
    }

    public static function getinternals()
    {
        return ["crowdoubtNEET", "crowdoubtJEE", "crowdoubtBoards"];
    }

    public static function isinternal($username)
    {
        if (in_array($username, $this->internal)) {
            return true;
        } else {
            return false;
        }
    }
}
