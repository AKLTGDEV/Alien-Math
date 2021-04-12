<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Videos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->increments("id");
            $table->string("filename");
            $table->string("uploader");
            $table->string("encname")->index();
            $table->string("searchterm")->nullable();
            $table->integer("attached")->default(0);
            $table->string("MCQ")->default("[]");
            $table->string("SAQ")->default("[]");
            $table->string("SQA")->default("[]");

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
        Schema::dropIfExists("videos");
    }
}
