<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Signups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('signups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('event_id');
            $table->integer('class_id');
            $table->integer('role_id');
            $table->string('sets')->default('');
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
        Schema::dropIfExists('signups');
    }
}
