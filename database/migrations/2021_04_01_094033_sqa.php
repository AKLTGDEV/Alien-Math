<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Sqa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('SQA', function (Blueprint $table) {
            $table->increments("id");
            $table->enum("type", [
                "P1", "P2", "P3", "P4", "P5", "P6",
                "S1", "S2", "S3", "S4",
            ]);
            $table->enum("difficulty", [
                1, 2, 3
            ]);
            $table->integer("rating")->default(0);

            $table->string("O1");
            $table->string("O2");
            $table->string("O3");
            $table->string("O4");

            $table->string("topics");
            $table->longText("digest")->nullable();
            $table->string("uploader"); //username

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
        Schema::dropIfExists("SQA");
    }
}
