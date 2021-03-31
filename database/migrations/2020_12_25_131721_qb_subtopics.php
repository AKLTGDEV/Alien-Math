<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class QbSubtopics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qb_subtopics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('author');
            $table->string('name');
            $table->integer('parent');
            $table->integer('posts')->default(0);
            $table->string('list')->default("[]");
            
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
        Schema::dropIfExists('qb_subtopics');
    }
}
