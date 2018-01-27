<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Guilds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guilds', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 250);
            $table->string('slug', 250);
            $table->integer('megaserver');
            $table->integer('platform');
            $table->json('admins');
            $table->integer('owner_id');
            $table->string('image', 250);
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
        Schema::dropIfExists('guilds');
    }
}
