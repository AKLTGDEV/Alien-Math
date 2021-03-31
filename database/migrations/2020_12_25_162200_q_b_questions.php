<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class QBQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qb_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('author');
            $table->string('title')->nullable(true);
            $table->integer('type'); // 1 for MCQ, 2 for Subjective
            $table->string('encname')->unique();
            $table->integer('topic');
            $table->integer('subtopic')->default(null);
            
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
        Schema::dropIfExists('qb_questions');
    }
}
