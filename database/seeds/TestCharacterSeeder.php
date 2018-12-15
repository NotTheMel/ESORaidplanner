<?php

use App\Character;
use App\Utility\Classes;
use App\Utility\Roles;
use Illuminate\Database\Seeder;

class TestCharacterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Character::query()->insert([
            'id' => 1,
            'user_id' => 2,
            'name' => 'Test Character',
            'class' => Classes::WARDEN,
            'role' => Roles::STAMINA_DD,
            'sets' => json_encode(['Armor of the Veiled Heritance', 'Flame Blossom']),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        Character::query()->insert([
            'id' => 2,
            'user_id' => 2,
            'name' => 'Test Character 2',
            'class' => Classes::TEMPLAR,
            'role' => Roles::HEALER,
            'sets' => json_encode(['Roar of Alkosh', 'Tormentor']),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
