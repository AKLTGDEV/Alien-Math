<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class cleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup local storage';

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
        Storage::deleteDirectory("profilepx");
        Storage::deleteDirectory("bio");
        Storage::deleteDirectory("posts");
        Storage::deleteDirectory("images");
        /*Storage::deleteDirectory("worksheets");
        Storage::deleteDirectory("ws_body");
        Storage::deleteDirectory("ws_opts");*/
        Storage::deleteDirectory("WS");
        Storage::deleteDirectory("tags");
        Storage::deleteDirectory("user_tags");
        Storage::deleteDirectory("actilog");
        Storage::deleteDirectory("classrooms");
        Storage::deleteDirectory("user_ext");
        Storage::deleteDirectory("indices");
        Storage::deleteDirectory("answers");
        Storage::deleteDirectory("dailyrecord");
        Storage::deleteDirectory("wsa_metrics");
        Storage::deleteDirectory("tag_req");
        Storage::deleteDirectory("docs");
        Storage::deleteDirectory("TS");
        Storage::deleteDirectory("QB_questions");
        Storage::deleteDirectory("QB_tests");
        Storage::deleteDirectory("saq");
        Storage::deleteDirectory("sqa");
        Storage::deleteDirectory("rating_changes");
        Storage::deleteDirectory("videos");
    }
}
