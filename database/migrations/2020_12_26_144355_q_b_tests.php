<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class QBTests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qb_tests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('author');
            $table->string('title')->nullable(false);
            $table->string('encname')->unique();
            $table->integer('topic');
            $table->integer('subtopic');
            
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
        Schema::dropIfExists('qb_tests');
    }
}
