<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Docuploads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docuploads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cid');
            $table->string('original_name');
            $table->string('enc_name')->unique();
            $table->string('title');
            $table->string('notes')->nullable();
            $table->integer('time');
            $table->integer('accepted')->default(0);
            
            $table->string('poster');
            $table->string('staff')->nullable();
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
        Schema::dropIfExists('docuploads');
    }
}
