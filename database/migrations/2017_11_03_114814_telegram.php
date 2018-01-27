<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Telegram extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telegram', function (Blueprint $table) {
            $table->string('username');
            $table->integer('menu_id')->nullable();
            $table->integer('guild_id')->nullable();
            $table->integer('event_id')->nullable();
            $table->string('role')->nullable();
            $table->string('class')->nullable();
            $table->string('support_sets')->nullable();
            $table->text('comment')->nullable();
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
        Schema::dropIfExists('telegram');
    }
}
