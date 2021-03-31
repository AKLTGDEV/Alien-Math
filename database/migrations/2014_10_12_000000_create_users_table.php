<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;


class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {

            Schema::dropIfExists('users');

            $table->increments('id');
            $table->string('provider_name')->nullable();
            $table->string('provider_id')->nullable();
            $table->string('api_token', 80)->unique()->nullable()->default(null);
            $table->string('avatar')->nullable();
            $table->string('name');
            $table->string('username')->unique()->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->dateTime('email_verified_at')->nullable(true);
            $table->integer('rating')->default(0);
            /*$table->integer('image')->nullable(true);
            $table->string('bio')->nullable(true);
            $table->string('tags')->default("[]");
            $table->string('answers')->default("{}");*/
            $table->integer('answers_t')->default(0);
            $table->integer('answers_f')->default(0);
            $table->integer('nos_Q')->default(0);
            $table->integer('nos_A')->default(0);
            $table->string('following')->default("[]");
            $table->integer('nos_following')->default(0);
            $table->string('followers')->default("[]");
            $table->integer('nos_followers')->default(0);
            $table->string('ws_posted')->default("[]");
            $table->string('ws_attempted')->default("[]");
            $table->string('classrooms')->default("[]");
            $table->string('ts_created')->default("[]");
            $table->string('ts_bought')->default("[]");
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
