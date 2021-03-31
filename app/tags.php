<?php

namespace App;

use App\PostModel;
use App\TagsModel;
use App\posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use TeamTNT\TNTSearch\TNTSearch;


class tags
{

    public static function exists($tagname)
    {
        if (TagsModel::where('name', '=', $tagname)->first()) {
            return true;
        } else {
            return false;
        }
    }

    public static function allposts($tagname)
    {
        $postlist_ret = array();

        $tag = TagsModel::where('name', '=', $tagname)->first();
        //$tag_posts_ids = json_decode($tag->posts);
        $tag_posts_ids = json_decode(Storage::get("tags/" . md5($tag->name)), true);

        foreach ($tag_posts_ids as $postid) {
            $post = PostModel::where('id', $postid)->first();
            $author = UserModel::where('id', $post->author)->first();

            $self = Auth::user();

            if (posts::check_if_answered($self, $post)) {
                //$answered_list = json_decode($self->answers, true);
                $answered_list = json_decode(Storage::get('answers/' . $self->username), true);
                $given = $answered_list["q" . $post->id];
            } else {
                $given = null;
            }

            $post__ = posts::get($post->id);
            array_push($postlist_ret, $post__);
        }

        return $postlist_ret;
    }

    public static function newpostrecord($tagname, $pid)
    {
        $tag_records = Storage::get("tags/" . md5($tagname));
        $tag_records = json_decode($tag_records, true);
        array_push($tag_records, $pid);
        Storage::put("tags/" . md5($tagname), json_encode($tag_records));
    }

    public static function newtag($tagname)
    {
        $newtag = new TagsModel;
        $newtag->name = $tagname;
        $newtag->save();
        Storage::put("tags/" . md5($tagname), "[]");

        $tnt = new TNTSearch;
        $tnt->loadConfig([
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', '127.0.0.1'),
            'database'  => env('DB_DATABASE', 'forge'),
            'username'  => env('DB_USERNAME', 'forge'),
            'password'  => env('DB_PASSWORD', ''),
            'storage'   => storage_path('app') . "/indices//",
        ]);
        $tnt->selectIndex("tags.index");
        $index = $tnt->getIndex();
        $index->insert([
            'id' => $newtag->id,
            'name' => $newtag->name
        ]);
    }

    public static function tagfollower_new($tagname)
    {
        $tag = TagsModel::where("name", $tagname)->first();
        $tag->followers++;
        $tag->save();
    }

    public static function tagfollower_rem($tagname)
    {
        $tag = TagsModel::where("name", $tagname)->first();
        $tag->followers--;
        $tag->save();
    }

    public static function top20()
    {
        /**
         * Return the top 20 tags based on 
         * the user's location and other 
         * such parameters. The returned 
         * listr will be used to populate 
         * the autocomplete boxes wherever 
         * user enters tags.
         * 
         * FIXME TODO
         */

        /**
         * For now just return the top 20 most popular tags.
         */
        $tags = TagsModel::orderBy('net', 'inc')->get();
        $ret = [];
        if (count($tags) < 20) {
            foreach ($tags as $tag) {
                array_push($ret, $tag->name);
            }
        } else {
            for ($i = 1; $i <= 20; $i++) {
                $tag = $tags[$i - 1];
                array_push($ret, $tag->name);
            }
        }

        return $ret;

        //return "SHLOB IN MI KNOB";
    }

    public static function gather_old(Request $request, $tag) // old function
    {
        /**
         * 
         * TARGET: store the list of relevant posts/WS of the tag in a 
         * session array (if necessary), and return the batch of 20.
         * 
         * Chck if the session array is ready. If not, CREATE IT. IF it is already there, check the 
         * current pointer. (the previous batch number). If there are a minimum of 20 items on the 
         * next batch, deliver the batch. Else, RECREATE the session.
         * 
         * 
         * REDUNDANT
         */

        /**
         * variables:
         *     tag_<tag name>_batch
         */

        $BATCH_SIZE = 20;

        if ($request->session()->has("tag_" . $tag . "_batch")) {
            // Get the items from the next batch,
            // increase the session variable

            $batch_no = $request->session()->get("tag_" . $tag . "_batch", 0);

            // Check if enough posts are available
            $first = (($BATCH_SIZE) * ($batch_no)) + 1;
            $last = ($BATCH_SIZE) * ($batch_no + 1);
            $posts_obj = PostModel::where('tags', 'like', '%' . $tag . '%')->orderBy('created_at', 'inc');
            $posts_count = $posts_obj->count();
            if ($posts_count < $last) {
                $request->session()->put("tag_" . $tag . "_batch", 0);

                //return "<$batch_no> OUT OF BOUND";

                return tags::gather($request, $tag); // Recursion biitch
            } else {
                $allposts = $posts_obj->get();
                $request->session()->put("tag_" . $tag . "_batch", $batch_no + 1);

                //return "<$batch_no> IN BOUND, [$first-$last]";
                $return_list = []; // fill it with IDS
                for ($i = $first; $i <= $last; $i++) {
                    $current_post = $allposts[$i - 1];
                    array_push($return_list, posts::get($current_post->id));
                    //array_push($return_list, $current_post->id);
                }

                return json_encode($return_list);
            }


            $request->session()->put("tag_" . $tag . "_batch", $batch_no + 1);
            return $batch_no;
        } else {
            $request->session()->put("tag_" . $tag . "_batch", 0);
            //return "BHINDI";
            return tags::gather($request, $tag); // Recursion biitch
        }
    }

    public static function gather(Request $request, $tag)
    {
        $idx = json_decode($request->idx, true);
        $nf_content = tags::gather_items(Auth::user(), $tag, $idx);

        return [
            'result' => $nf_content['result'],
            'idx' => $nf_content['idx'],
        ];
    }

    public static function gather_items($user, $tag, $idx)
    {
        $STEP1_ADD_LIMIT = 10;
        $STEP2_ADD_LIMIT = 5;

        $NEWSFEED = [];


        /**
         * Step : Get the recent and most popular posts
         */
        $posts_interim = [];
        $posts_usertag = PostModel::where('tags', 'like', '%' . $tag . '%')
            ->orderBy('attempts', 'inc')
            ->orderBy('id', 'desc')
            ->get();

        $k = 1;
        while (count($posts_usertag) >= $k) {
            array_push($posts_interim, $posts_usertag[$k - 1]);
            $k++;
        }

        shuffle($posts_interim);

        $added = 0;
        foreach ($posts_interim as $p_interim) {
            if ($added == $STEP1_ADD_LIMIT) {
                break;
            }

            if (!tags::item_seen($idx, "post", $p_interim->id)) {
                // The item is not seen yet. Add it to array.
                $added++;
                array_push($NEWSFEED, posts::get($p_interim->id));

                array_push($idx, [
                    "type" => "post",
                    "id" => $p_interim->id,
                ]);
            }
        }



        /**
         * Step 2: Get the recent and most popular Worksheets
         */
        $ws_interim = [];
        $ws_usertag = WorksheetModel::where('tags', 'like', '%' . $tag . '%')
            ->orderBy('attempts', 'inc')
            ->orderBy('id', 'desc')
            ->get();

        $k = 1;
        while (count($ws_usertag) >= $k) {
            array_push($ws_interim, $ws_usertag[$k - 1]);
            $k++;
        }

        shuffle($ws_interim);

        $added = 0;
        foreach ($ws_interim as $ws_interim_current) {
            if ($added == $STEP2_ADD_LIMIT) {
                break;
            }

            if (!tags::item_seen($idx, "ws", $ws_interim_current->id)) {
                // The item is not seen yet. Add it to array.
                $added++;
                array_push($NEWSFEED, worksheets::get($ws_interim_current->id));

                array_push($idx, [
                    "type" => "ws",
                    "id" => $ws_interim_current->id,
                ]);
            }
        }


        shuffle($NEWSFEED);
        return [
            "result" => $NEWSFEED,
            "idx" => $idx,
        ];
    }

    public static function item_seen($idx_list, $itemtype, $itemid)
    {
        foreach ($idx_list as $idx_item) {
            if ($idx_item['type'] == $itemtype && $idx_item['id'] == $itemid) {
                return true;
            }
        }

        return false;
    }
}
