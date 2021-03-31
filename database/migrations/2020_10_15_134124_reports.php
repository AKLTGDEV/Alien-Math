<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Reports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('item_id');
            $table->string('from');
            
            $table->boolean('wrong_ans')->default(false);
            $table->boolean('ab_lang')->default(false);
            $table->boolean('wrong_topic')->default(false);
            $table->boolean('unc_cont')->default(false);
            
            $table->string('data')->nullable();
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
        Schema::dropIfExists('reports');
    }
}
