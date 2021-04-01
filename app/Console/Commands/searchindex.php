<?php

namespace App\Console\Commands;

use App\PostModel;
use App\posts;
use App\TagsModel;
use App\UserModel;
use App\WorksheetModel;
use Illuminate\Console\Command;
use TeamTNT\TNTSearch\TNTSearch;

class searchindex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'searchindex';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build Search Indices';

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

        $this->info("  Indexing..\n");

        $tnt = new TNTSearch;

        $tnt->loadConfig([
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', '127.0.0.1'),
            'database'  => env('DB_DATABASE', 'co'),
            'username'  => env('DB_USERNAME', 'co_admin'),
            'password'  => env('DB_PASSWORD', '1234'),
            'storage'   => storage_path('app') . "/indices//",
        ]);

        $classic = true;

        if (!$classic) {

            $user_indexer = $tnt->createIndex('users.index');
            foreach (UserModel::all() as $user) {
                $user_indexer->insert([
                    'id' => $user->id,
                    'username' => $user->username,
                ]);

                $this->info(" ** USER IDX ** ..$user->username\n");
            }

            $post_indexer = $tnt->createIndex('posts.index');
            foreach (PostModel::all() as $post) {
                $post_indexer->insert([
                    'id' => $post->id,
                    'title' => $post->title,
                    //'body' => posts::getbody($post->text)
                ]);

                $this->info(" ** POST IDX ** ..$post->text\n");
            }

            $ws_indexer = $tnt->createIndex('ws.index');
            foreach (WorksheetModel::all() as $ws) {
                $ws_indexer->insert([
                    'id' => $ws->id,
                    'title' => $ws->title,
                ]);

                $this->info(" ** WS IDX ** ..$ws->title\n");
            }

            $tags_indexer = $tnt->createIndex('tags.index');
            foreach (TagsModel::all() as $tag) {
                $tags_indexer->insert([
                    'id' => $tag->id,
                    'name' => $tag->name,
                ]);

                $this->info(" ** TAG IDX ** ..$tag->name\n");
            }
        } else {
            $user_indexer = $tnt->createIndex('users.index');
            $user_indexer->query('SELECT id, name, username FROM users;');
            $user_indexer->run();

            $post_indexer = $tnt->createIndex('posts.index');
            $post_indexer->query('SELECT id, title FROM posts;');
            $post_indexer->run();

            $ws_indexer = $tnt->createIndex('ws.index');
            $ws_indexer->query('SELECT id, title FROM worksheets;');
            $ws_indexer->run();

            $tags_indexer = $tnt->createIndex('tags.index');
            $tags_indexer->query('SELECT id, name FROM tags;');
            $tags_indexer->run();

            $saq_indexer = $tnt->createIndex('saq.index');
            $saq_indexer->query('SELECT id, digest FROM SAQ;');
            $saq_indexer->run();

            $sqa_indexer = $tnt->createIndex('sqa.index');
            $sqa_indexer->query('SELECT id, digest FROM SQA;');
            $sqa_indexer->run();
        }
    }
}
