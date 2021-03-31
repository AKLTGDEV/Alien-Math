<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WorksheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('worksheets');

        Schema::create('worksheets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->nullable(true);
            $table->string('title');
            $table->bigInteger('nos')->default(0);
            $table->string('images')->nullable(true);
            $table->string('ws_name')->unique();
            $table->string('tags');
            /*$table->string('opts');
            $table->string('correctopts');*/
            $table->bigInteger('author');
            $table->bigInteger('attempts')->default(0);
            $table->string('attemptees')->default("[]");
            $table->bigInteger('mins')->default(0);
            $table->string('invited')->default("[]");
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
        Schema::dropIfExists('posts');
    }
}
