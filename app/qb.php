<?php

namespace App;

use App\PostModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class qb
{
    public static function call($user, $request)
    {
        $idx = json_decode($request->idx, true);

        $content = qb::get($user, $idx);
        return [
            'result' => $content['result'],
            'idx' => $content['idx'],
        ];
    }

    public static function get($user, $idx)
    {
        $FEED = [];

        // Get all the user's posts
        $posts_interim = PostModel::where("author", $user->id)->get();
        $added = 0;
        foreach ($posts_interim as $p_interim) {
            if (!qb::item_seen($idx, "post", $p_interim->id)) {
                // The item is not seen yet. Add it to array.
                $added++;
                array_push($FEED, posts::get($p_interim->id));

                array_push($idx, [
                    "type" => "post",
                    "id" => $p_interim->id,
                ]);
            }
        }


        // Get all the posts user made inside the bank
        $posts_bank = QBquestionsModel::where('author', Auth::user()->id)->get();
        $added = 0;
        foreach ($posts_bank as $p_bank) {
            if (!qb::item_seen($idx, "BANKpost", $p_bank->id)) {
                // The item is not seen yet. Add it to array.
                $added++;
                array_push($FEED, $p_bank->content());

                array_push($idx, [
                    "type" => "BANKpost",
                    "id" => $p_bank->id,
                ]);
            }
        }



        return [
            "result" => $FEED,
            "idx" => $idx,
        ];
    }

    public static function get_under_topic($user, $idx, $topicid)
    {
        $FEED = [];
        // Get all the posts user made under this topic
        $posts_bank = QBquestionsModel::where('author', Auth::user()->id)
            ->where("topic", $topicid)
            ->get();
        $added = 0;
        foreach ($posts_bank as $p_bank) {
            if (!qb::item_seen($idx, "BANKpost", $p_bank->id)) {
                // The item is not seen yet. Add it to array.
                $added++;
                array_push($FEED, $p_bank->content());

                array_push($idx, [
                    "type" => "BANKpost",
                    "id" => $p_bank->id,
                ]);
            }
        }



        return [
            "result" => $FEED,
            "idx" => $idx,
        ];
    }

    public static function get_under_subtopic($user, $idx, $topicid)
    {
        $FEED = [];
        // Get all the posts user made under this topic
        $posts_bank = QBquestionsModel::where('author', Auth::user()->id)
            ->where("subtopic", $topicid)
            ->get();
        $added = 0;
        foreach ($posts_bank as $p_bank) {
            if (!qb::item_seen($idx, "BANKpost", $p_bank->id)) {
                // The item is not seen yet. Add it to array.
                $added++;
                array_push($FEED, $p_bank->content());

                array_push($idx, [
                    "type" => "BANKpost",
                    "id" => $p_bank->id,
                ]);
            }
        }



        return [
            "result" => $FEED,
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



    // OTHER METHODS:

    public static function new_mcq($request)
    {
        // Make an entry in the DB table
        $question = new QBquestionsModel;
        $encname = $question->mcq(
            Auth::user()->id,
            $request->title,
            $request->topic,
            $request->subtopic
        );

        //Store data in local storage
        Storage::put("QB_questions/$encname", json_encode($request->all()));

        // Update topic, subtopic data
        $topic = QBTopicsModel::where("id", $request->topic)->first();
        $topic->addpost($question->id);
        $subtopic = QBSubTopicsModel::where("id", $request->subtopic)->first();
        $subtopic->addpost($question->id);

        return redirect()->route('qbank_index');
    }

    public static function new_subjective($request)
    {
        // Make an entry in the DB table
        $question = new QBquestionsModel;
        $encname = $question->subjective(
            Auth::user()->id,
            $request->title,
            $request->topic,
            $request->subtopic
        );

        //Store data in local storage
        Storage::put("QB_questions/$encname", json_encode($request->all()));

        // Update topic, subtopic data
        $topic = QBTopicsModel::where("id", $request->topic)->first();
        $topic->addpost($question->id);
        $subtopic = QBSubTopicsModel::where("id", $request->subtopic)->first();
        $subtopic->addpost($question->id);

        return redirect()->route('qbank_index');
    }
}
