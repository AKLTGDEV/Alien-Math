<?php

namespace App\Console\Commands;

use App\numbersT;
use App\relations as UserRelations;
use App\UserModel;
use Illuminate\Console\Command;

class relations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'relations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate User-relations';

    /**
     * Create a new command instance.
     *
     * @return void
     */
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
        $tofollow = numbersT::seed_tofollow();

        $this->info(count($allusers) . ' users in total.');
        //$tofollow = $this->ask('No. of users to follow');
        $this->info($tofollow . ' users to follow.');

        foreach ($allusers as $user) {
            $this->info('Generating relations for: ' . $user->username);

            $tofollow_ids = range(1, count($allusers));
            shuffle($tofollow_ids);
            $tofollow_ids = array_slice($tofollow_ids, 0, $tofollow);

            foreach ($tofollow_ids as $id) {
                $person = UserModel::where('id', $id)->first();
                UserRelations::follow($user->username, $person->username);
                $this->info($user->username." || ".$person->username);
            }

            //$this->info('');
        }
    }
}
