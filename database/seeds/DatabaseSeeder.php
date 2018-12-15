<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if (env('APP_ENV') === 'testing') {
            $this->call([
                TestUserSeeder::class,
                TestGuildSeeder::class,
                TestEventSeeder::class,
                TestCharacterSeeder::class,
                TestTeamSeeder::class,
            ]);
        }
    }
}
