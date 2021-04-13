<?php

namespace App;

use App\utils\Rating;
use Carbon\Carbon;
use DebugBar\DebugBar;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class RatingsModel extends Model
{
    protected $table = 'ratings';

    public function __construct()
    {
        // Do nothing
    }

    public static function new($username, $topicid, $rating)
    {
        $r = RatingsModel::where("of", $username)
            ->where("topic", $topicid)
            ->first();
        if ($r == null) {
            $r = new RatingsModel;
            $r->of = $username;
            $r->topic = $topicid;
            $r->rating = $rating;
            $r->change = 0;
            $r->save();

            Storage::put("rating_changes/$r->id", "[]");
        }
    }


    // CHANGE IN RATING BASED ON WHICH TYPE OF QUESTION IS ATTEMPTED:

    public function MCQ($id, $correct)
    {
        $q = PostModel::where("id", $id)->first();
        if ($q == null) {
            return false;
        } else {
            $ratings = new Rating(
                $this->rating, // Student's rating
                $q->rating, // Question's Rating
                $correct == true ? Rating::WIN : Rating::LOST,
                $correct == true ? Rating::LOST : Rating::WIN,
            );

            $results = $ratings->getNewRatings();
            $this->rating = round($results['a']);
            $rating_changes = json_decode(Storage::get("rating_changes/$this->id"));
            $rating_changes[] = [
                "change" => ($this->change) + 1,
                "rating" => $this->rating,
                "on" => Carbon::now()->toDateTimeString(),
            ];
            $this->change++;
            $this->save();
            Storage::put("rating_changes/$this->id", json_encode($rating_changes));

            $q->rating = round($results['b']);
            $q->save();

            return true;
        }
    }


    public function SAQ($id, $correct)
    {
        $q = SAQ::where("id", $id)->first();
        if ($q == null) {
            return false;
        } else {
            $ratings = new Rating(
                $this->rating, // Student's rating
                $q->rating, // Question's Rating
                $correct == true ? Rating::WIN : Rating::LOST,
                $correct == true ? Rating::LOST : Rating::WIN,
            );

            $results = $ratings->getNewRatings();
            $this->rating = round($results['a']);
            $rating_changes = json_decode(Storage::get("rating_changes/$this->id"));
            $rating_changes[] = [
                "change" => ($this->change) + 1,
                "rating" => $this->rating,
                "on" => Carbon::now()->toDateTimeString(),
            ];
            $this->change++;
            $this->save();
            Storage::put("rating_changes/$this->id", json_encode($rating_changes));

            $q->rating = round($results['b']);
            $q->save();

            return true;
        }
    }

    public function SQA($id, $correct)
    {
        $q = SQA::where("id", $id)->first();
        if ($q == null) {
            return false;
        } else {
            $ratings = new Rating(
                $this->rating, // Student's rating
                $q->rating, // Question's Rating
                $correct == true ? Rating::WIN : Rating::LOST,
                $correct == true ? Rating::LOST : Rating::WIN,
            );

            $results = $ratings->getNewRatings();
            $this->rating = round($results['a']);
            $rating_changes = json_decode(Storage::get("rating_changes/$this->id"));
            $rating_changes[] = [
                "change" => ($this->change) + 1,
                "rating" => $this->rating,
                "on" => Carbon::now()->toDateTimeString(),
            ];
            $this->change++;
            $this->save();
            Storage::put("rating_changes/$this->id", json_encode($rating_changes));

            $q->rating = round($results['b']);
            $q->save();

            return true;
        }
    }

    public function lastchange()
    {
        $record = json_decode(Storage::get("rating_changes/$this->id"), true);

        switch (count($record)) {
            case 0:
                return $this->rating;

            case 1:
                return 0;

            default:                
                $last = $record[count($record) - 1];
                $second_last = $record[count($record) - 2];

                return $last['rating'] - $second_last['rating'];
        }
    }
}
