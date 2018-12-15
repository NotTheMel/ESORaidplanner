<?php

use App\Event;
use App\Signup;
use App\Utility\Classes;
use App\Utility\Roles;
use Illuminate\Database\Seeder;

class TestEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dt = new DateTime('@'.strtotime('+7 days'));

        Event::query()->insert([
            'id' => 1,
            'name' => 'Test Event',
            'description' => 'This is a test event.',
            'start_date' => $dt->format('Y-m-d H:i:s'),
            'guild_id' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        Signup::query()->insert([
            'user_id' => 1,
            'event_id' => 1,
            'class_id' => Classes::DRAGONKNIGHT,
            'role_id' => Roles::TANK,
            'sets' => json_encode(['Ebon Armory', 'Roar of Alkosh']),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
