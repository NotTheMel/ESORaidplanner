<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoreDiscordStuff extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function($table) {
            $table->string('discord_id')->nullable();
        });
        Schema::table('guilds', function($table) {
            $table->string('discord_channel_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function($table) {
            $table->dropColumn('discord_id');
        });
        Schema::table('guilds', function($table) {
            $table->dropColumn('discord_channel_id');
        });
    }
}
