<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecurringTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recurring', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('guild_id');
            $table->dateTime('start_date');
            $table->string('timezone');
            $table->string('interval');
            $table->integer('max_create_ahead')->default(1);
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('tags');
            $table->integer('latest_event');
            $table->dateTime('latest_event_date');
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
        Schema::dropIfExists('recurring');
    }
}
