<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class ConvertJsonObjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $notifications = \App\Notification\Notification::all();

        foreach ($notifications as $notification) {
            $tags = explode(',', $notification->tags);
            foreach ($tags as &$tag) {
                $tag = trim($tag);
            }
            $notification->tags = json_encode($tags);
            $notification->save();
        }

        $events = \App\Event::all();

        foreach ($events as $event) {
            $tags = explode(',', $event->tags);
            foreach ($tags as &$tag) {
                $tag = trim($tag);
            }
            $event->tags = json_encode($tags);
            $event->save();
        }

//        $events = \App\RepeatableEvent::all();
//
//        foreach ($events as $event) {
//            $tags = explode(',', $event->tags);
//            foreach ($tags as &$tag) {
//                $tag = trim($tag);
//            }
//            $event->tags = json_encode($tags);
//            $event->save();
//        }

        $signups = \App\Signup::all();

        foreach ($signups as $signup) {
            $tags = explode(',', $signup->sets);
            foreach ($tags as &$tag) {
                $tag = trim($tag);
            }
            $signup->sets = json_encode($tags);
            $signup->save();
        }

        $characters = \App\Character::all();

        foreach ($characters as $character) {
            $tags = explode(',', $character->sets);
            foreach ($tags as &$tag) {
                $tag = trim($tag);
            }
            $character->sets = json_encode($tags);
            $character->save();
        }

        DB::statement('ALTER TABLE characters CHANGE sets sets JSON NOT NULL');
        DB::statement('ALTER TABLE signups CHANGE sets sets JSON NOT NULL');
        DB::statement('ALTER TABLE events CHANGE tags tags JSON NOT NULL');
        DB::statement('ALTER TABLE repeatable_events CHANGE tags tags JSON NOT NULL');
        DB::statement('ALTER TABLE hooks CHANGE tags tags JSON NOT NULL');
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
