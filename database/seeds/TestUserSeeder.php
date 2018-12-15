<?php

use App\User;
use Illuminate\Database\Seeder;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::query()->insert([
            'id' => 1,
            'name' => 'Test Admin',
            'email' => str_random(10).'@esoraidplanner.com',
            'password' => bcrypt('secret'),
            'timezone' => 'Europe/Amsterdam'
        ]);
        User::query()->insert([
            'id' => 2,
            'name' => 'Test Member',
            'email' => str_random(10).'@esoraidplanner.com',
            'password' => bcrypt('secret'),
            'timezone' => 'Europe/Amsterdam'
        ]);
        User::query()->insert([
            'id' => 3,
            'name' => 'Test Non-member',
            'email' => str_random(10).'@esoraidplanner.com',
            'password' => bcrypt('secret'),
            'timezone' => 'Europe/Amsterdam'
        ]);
    }
}
