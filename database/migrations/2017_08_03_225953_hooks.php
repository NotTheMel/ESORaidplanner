<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Hooks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hooks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 250);
            $table->integer('type');
            $table->string('token', 250)->nullable();
            $table->string('chat_id', 50)->nullable();
            $table->string('url', 500)->nullable();
            $table->integer('call_time_diff')->nullable();
            $table->integer('signup_count')->nullable();
            $table->text('message');
            $table->boolean('active');
            $table->integer('guild_id')->nullable();
            $table->integer('user_id')->nullable();
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
        Schema::dropIfExists('hooks');
    }
}
