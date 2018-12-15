<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetAllUtc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $events = \App\Event::all();

        foreach ($events as $event) {
            $dt = new DateTime($event->start_date, new DateTimeZone('Europe/Amsterdam'));
            $dt->setTimezone(new DateTimeZone('UTC'));
            $event->start_date = $dt->format('Y-m-d H:i:s');
            $event->save();
        }

//        $repeatables = \App\RepeatableEvent::all();
//
//        foreach ($repeatables as $event) {
//            $dt = new DateTime($event->latest_event, new DateTimeZone('Europe/Amsterdam'));
//            $dt->setTimezone(new DateTimeZone('UTC'));
//            $event->latest_event = $dt->format('Y-m-d H:i:s');
//            $event->save();
//        }

        $signups = \App\Signup::all();

        foreach ($signups as $event) {
            $dt = new DateTime($event->created_at, new DateTimeZone('Europe/Amsterdam'));
            $dt->setTimezone(new DateTimeZone('UTC'));
            try {
                \App\Signup::query()
                    ->where('id', '=', $event->id)
                    ->update(['created_at' => $dt->format('Y-m-d H:i:s')]);
            } catch (Exception $e) {

            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
