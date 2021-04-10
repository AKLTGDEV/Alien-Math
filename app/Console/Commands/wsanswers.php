<?php

namespace App\Console\Commands;

use App\activitylog;
use App\numbersT;
use App\RatingsModel;
use App\UserModel;
use App\WorksheetModel;
use App\worksheets;
use App\wsAttemptsModel;
use Faker\Factory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class wsanswers extends Command
{
    protected $signature = 'wsanswers';
    protected $description = 'Seed random WS answers by users';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $allusers = UserModel::all();
        $nos_ws_total = count(WorksheetModel::all());
        $nos_answers = numbersT::nos_wsanswers();

        /**
         * Each user will answer $nos_answers random worksheets.
         */

        foreach ($allusers as $user) {
            $this->info("user: " . $user->name);
            $ws_id_pool = [];
            while (count($ws_id_pool) < $nos_answers) {
                $x = rand(1, $nos_ws_total);
                if (in_array($x, $ws_id_pool) == false) {
                    array_push($ws_id_pool, $x);
                }
            }

            foreach ($ws_id_pool as $wsid) {
                $worksheet = WorksheetModel::where('id', $wsid)->first();

                $attempt = new wsAttemptsModel;
                $attempt->wsid = $worksheet->id;
                $attempt->attemptee = $user->id;

                $results_pool = ["T", "F", "L"];
                $results = [];
                for ($i = 1; $i <= $worksheet->nos; $i++) {
                    $current_result = $results_pool[rand(0, 2)];

                    switch ($current_result) {
                        case 'T':
                            $attempt->right++;
                            break;

                        case 'F':
                            $attempt->wrong++;
                            break;

                        case 'L':
                            $attempt->left++;
                            break;

                        default:
                            // This is not right
                            break;
                    }

                    $results[] = $current_result;
                }

                $attempt->save();
                //Save results in local storage, not DB
                Storage::put("wsa_metrics/$attempt->id/results", json_encode($results));

                $ws_info = json_decode(Storage::get("WS/$worksheet->ws_name"), true);
                $questions = $ws_info['content'];

                /** 
                 * Now save the info in local storage
                 * 
                 */
                $clock_hits = [];
                for ($j = 1; $j <= $worksheet->nos; $j++) {
                    $clock_hits[] = rand(1, 120);
                }
                Storage::put("wsa_metrics/$attempt->id/clock_hits", json_encode($clock_hits));


                $answers = [];
                for ($k = 1; $k <= $worksheet->nos; $k++) {
                    $status = $results[$k - 1];

                    if ($status == "L") {
                        $answers[] = null;
                    } else {
                        // See what type of question it is
                        $data = $ws_info['content'][$k - 1];
                        if ($data['type'] == "MCQ") {
                            if ($status == "T") {
                                $answers[] = $data['correct'];
                            } else {
                                $list_corrects = [1, 2, 3, 4];
                                $correct = 0;
                                foreach ($list_corrects as $c) {
                                    if ($c != $data['correct']) {
                                        $correct = $c;
                                        break;
                                    }
                                }

                                $answers[] = $correct;
                            }
                        } else if ($data['type'] == "SAQ") {
                            if ($status == "T") {
                                $answers[] = $data['correct'];
                            } else {
                                $faker = Factory::create();
                                $correct = $faker
                                    ->unique()
                                    ->sentence(10);
                                //Hope that it won't magically be the same as the correct answer

                                $answers[] = $correct;
                            }
                        } else if ($data['type'] == "SQA") {
                            $opts = $data['opts'];
                            $current_answer = [];

                            if ($status == "T") {
                                $current_answer[$opts[0]] = 1;
                                $current_answer[$opts[1]] = 2;
                                $current_answer[$opts[2]] = 3;
                                $current_answer[$opts[3]] = 4;
                            } else {
                                $order = [1, 2, 3, 4];
                                shuffle($order);

                                $current_answer[$opts[0]] = $order[0];
                                $current_answer[$opts[1]] = $order[1];
                                $current_answer[$opts[2]] = $order[2];
                                $current_answer[$opts[3]] = $order[3];
                            }

                            $answers[] = $current_answer;
                        }
                    }

                    $question = $questions[$k - 1];
                    foreach ($question['topics'] as $tid) {
                        RatingsModel::new($user->username, $tid, 1000);
                        
                        $r = RatingsModel::where("of", $user->username)
                            ->where("topic", $tid)
                            ->first();

                        if ($status == "L" || $status == "F") {
                            $r_stat = false;
                        } else {
                            $r_stat = true;
                        }
                        switch ($question['type']) {
                            case 'MCQ':
                                $r->MCQ($question['id'], $r_stat);
                                break;

                            case 'SAQ':
                                $r->SAQ($question['id'], $r_stat);
                                break;

                            case 'SQA':
                                $r->SQA($question['id'], $r_stat);
                                break;

                            default:
                                // This is not okay
                                break;
                        }
                    }
                }

                $worksheet->attempts++;
                $worksheet->save();

                //Storage::put("wsa_metrics/$attempt->id/answers", "[]");
                Storage::put("wsa_metrics/$attempt->id/answers", json_encode($answers));

                activitylog::ans_ws($user->username, $worksheet->id);

                echo "WS ID " . $worksheet->id . "\n";
            }
        }
    }
}
