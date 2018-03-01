<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTagsToHooks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hooks', function($table) {
            $table->text('tags')->nullable();
        });

        Schema::table('events', function($table) {
            $table->text('tags')->nullable();
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
            $table->dropColumn('tags');
        });

        Schema::table('events', function($table) {
            $table->dropColumn('tags');
        });
    }
}
