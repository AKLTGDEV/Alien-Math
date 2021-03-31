<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class biocompact implements Rule
{
    public function __construct()
    {
        //
    }

    public function passes($attribute, $value)
    {
        if(strlen($value) > 25){
            return false;
        } else {
            return true;
        }
    }

    public function message()
    {
        return "Bio must be less than 25 characters";
    }
}
