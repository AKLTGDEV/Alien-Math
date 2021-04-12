<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            
            $table->enum("type", [
                "P1", "P2", "P3", "P4", "P5", "P6",
                "S1", "S2", "S3", "S4",
            ]);
            $table->enum("difficulty", [
                1, 2, 3
            ]);
            $table->integer("rating")->default(0);

            $table->string('image')->nullable(true);
            $table->string('slug')->nullable(true);
            $table->string('text')->unique();
            $table->string('tags');
            $table->string('opts');
            $table->bigInteger('correctopt');
            $table->bigInteger('author');
            $table->bigInteger('attempts')->default(0);
            /*$table->string('attemptees')->default("[]");*/
            $table->bigInteger('success')->default(0);
            $table->string('title')->default("--");

            $table->string("videos")->default("[]");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
