<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFavToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function($table) {
            $table->string('cover_image')->default('default_cover.jpg');
            $table->string('title')->default('');
            $table->text('description');
            $table->integer('race')->default(0);
            $table->integer('alliance')->default(0);
            $table->integer('class')->default(0);
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
            $table->dropColumn('race');
            $table->dropColumn('alliance');
            $table->dropColumn('class');
            $table->dropColumn('cover_image');
            $table->dropColumn('title');
            $table->dropColumn('description');
        });
    }
}
