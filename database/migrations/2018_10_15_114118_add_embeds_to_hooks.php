<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmbedsToHooks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hooks', function($table) {
            $table->integer('has_embeds')->default(0);
            $table->dropColumn('active');
            $table->dropColumn('signup_count');
            $table->dropColumn('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hooks', function($table) {
            $table->dropColumn('has_embeds');
        });
    }
}
