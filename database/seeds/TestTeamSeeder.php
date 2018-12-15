<?php

use App\Team;
use Illuminate\Database\Seeder;

class TestTeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Team::query()->insert([
            'name' => 'Test team',
            'guild_id' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
