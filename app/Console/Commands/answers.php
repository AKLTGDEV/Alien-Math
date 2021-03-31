<?php

namespace App\Console\Commands;

use App\numbersT;
use App\PostModel;
use App\posts;
use App\relations as UserRelations;
use App\UserModel;
use Illuminate\Console\Command;

class answers extends Command
{
    protected $signature = 'answers';
    protected $description = 'Seed random answers by user';

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
        $nos_questions_total = count(PostModel::all());
        $nos_answers = numbersT::nos_answers();

        /**
         * Each user will answer $nos_answers random questions.
         */

        foreach ($allusers as $user) {
            $q_id_pool = [];
            while (count($q_id_pool) < $nos_answers) {
                $x = rand(1, $nos_questions_total);
                if (in_array($x, $q_id_pool) == false) {
                    array_push($q_id_pool, $x);
                }
            }

            foreach ($q_id_pool as $qid) {
                $this->info($user->name . " ==> " . $qid);
                posts::answer($user->username, $qid, rand(1, 2));
            }
        }
    }
}
