<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Testseries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('testseries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->bigInteger('author');
            $table->bigInteger('nos')->default(0);
            $table->string('tags')->default("[]");
            $table->bigInteger('amount');
            $table->string('encname');

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
        Schema::dropIfExists('testseries');
    }
}
