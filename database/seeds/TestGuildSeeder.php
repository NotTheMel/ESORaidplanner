<?php

use App\Guild;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestGuildSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Guild::query()->insert([
            'id' => 1,
            'name' => 'Test Guild',
            'slug' => 'test-guild',
            'admins' => json_encode([1]),
            'owner_id' => 1,

        ]);

        DB::table(Guild::X_REF_USERS)->insert([
            'user_id' => 1,
            'guild_id' => 1,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        DB::table(Guild::X_REF_USERS)->insert([
            'user_id' => 2,
            'guild_id' => 1,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
