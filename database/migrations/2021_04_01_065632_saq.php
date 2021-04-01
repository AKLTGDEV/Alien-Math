<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Saq extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('SAQ', function (Blueprint $table) {
            $table->increments("id");
            $table->enum("type", [
                "P1", "P2", "P3", "P4", "P5", "P6",
                "S1", "S2", "S3", "S4",
            ]);
            $table->enum("difficulty", [
                1, 2, 3
            ]);

            $table->string("correct");
            $table->string("topics");
            $table->string("digest")->nullable();
            $table->string("uploader"); //username

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
        Schema::dropIfExists("SAQ");
    }
}
