<?php

namespace App\Console\Commands;

use App\activitylog;
use App\numbersT;
use App\UserModel;
use App\WorksheetModel;
use App\worksheets;
use Illuminate\Console\Command;

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
            $this->info("user: " . $user->name . "\n");
            $ws_id_pool = [];
            while (count($ws_id_pool) < $nos_answers) {
                $x = rand(1, $nos_ws_total);
                if (in_array($x, $ws_id_pool) == false) {
                    array_push($ws_id_pool, $x);
                }
            }

            foreach ($ws_id_pool as $wsid) {
                $worksheet = WorksheetModel::where('id', $wsid)->first();

                $ans = [];
                for ($i = 1; $i <= $worksheet->nos; $i++) {
                    array_push($ans, random_int(1, 4));
                }

                $opt_changes = [];
                for ($j = 1; $j <= $worksheet->nos; $j++) {
                    array_push($opt_changes, random_int(5, 20));
                }

                $Ttaken = 50;

                $clock_hits = [];
                // Generate $nos random numbers, which all sums up to $ws->mins*60 -10
                $ch_sum_last = (($worksheet->mins) * 60) - 10;
                $sum = 0;
                for ($k = 1; $k <= $worksheet->nos; $k++) {
                    $current = random_int(1, 10);
                    array_push($clock_hits, $current);
                    $sum += $current;
                }

                for ($l = 1; $l <= count($clock_hits); $l++) {
                    $clock_hits[$l - 1] /= $sum;
                }

                for ($p = 1; $p <= count($clock_hits); $p++) {
                    $clock_hits[$p - 1] *= $ch_sum_last;
                }

                for ($q = 1; $q <= count($clock_hits); $q++) {
                    $clock_hits[$q - 1] = round($clock_hits[$q - 1], 2);
                }


                $all = [
                    'wsid' => $wsid,
                    'ans' => json_encode($ans),
                    'clock_hits' => json_encode($clock_hits),
                    //'clock_hits' => '[]',
                    'opt_changes' => json_encode($opt_changes),
                    'Ttaken' => $Ttaken,
                ];

                echo worksheets::answer_submit_seed($all, $user) . "\n";

                activitylog::ans_ws($user->username, $worksheet->id);
            }
        }
    }
}
