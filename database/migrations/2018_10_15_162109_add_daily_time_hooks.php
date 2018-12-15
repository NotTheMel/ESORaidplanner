<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDailyTimeHooks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hooks', function($table) {
            $table->text('message')->nullable()->change();
            $table->string('daily_trigger_time')->nullable();
            $table->json('timezones')->nullable();
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
            $table->dropColumn('daily_trigger_time');
            $table->dropColumn('timezones');
        });
    }
}
