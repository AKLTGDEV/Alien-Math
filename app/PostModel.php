<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostModel extends Model
{
    protected $table = 'posts';

    public function getBody()
    {
        $post_body = posts::getbody($this->text);

        $img_present = false;
        if (strpos($post_body, '<img style=') !== false) {
            $img_present = true;
        }

        // Normalize the images
        $body_new = str_replace('<img style=', '<img id="postimg-' . $this->id . '" class="img-fluid" style=', $post_body);

        return $body_new;
    }
}
