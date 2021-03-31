<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWsAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ws_attempts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('random_id')->nullable(true);
            $table->bigInteger('wsid');
            $table->bigInteger('attemptee');
            $table->boolean('public')->default(false);
            $table->string('public_id')->nullable(true);
            $table->string('answers')->default("[]");
            $table->bigInteger('secs')->default(0);
            //$table->string('metrics')->default("[]");
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
        Schema::dropIfExists('ws_attempts');
    }
}
