<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Tagrequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tag_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('msg')->deafult("None");
            $table->integer('people')->default(0);
            $table->integer('status')->default(1);
            $table->integer('accepted')->default(0);
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
        Schema::dropIfExists('tag_requests');
    }
}
