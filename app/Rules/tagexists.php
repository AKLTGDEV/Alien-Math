<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\tags;

class tagexists implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $tags = explode(",", $value);
        foreach ($tags as $tag) {
            $tag = trim($tag);
            if (tags::exists($tag) == false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Some tags you entered don\'t exist';
    }
}
