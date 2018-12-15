<?php
/**
 * This file is part of the ESO-Database project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 *
 * @see https://eso-database.com
 * Created by woeler
 * Date: 9/19/18
 * Time: 12:59 PM
 */

namespace Tests\Feature\Model;


use App\Team;
use App\User;
use App\Utility\Classes;
use App\Utility\Roles;
use Tests\TestCase;

class TeamTest extends TestCase
{
    /**
     * @var Team
     */
    protected $team;

    /**
     * @var User
     */
    protected $user;

    public function setUp()
    {
        parent::setUp();

        $this->artisan('migrate:refresh', [
            '--seed' => true,
        ]);

        $this->team = Team::query()->find(1);
        $this->user = User::query()->find(2);
    }

    /** @test */
    public function addMemberTest()
    {
        $this->assertFalse($this->team->isMember($this->user));
        $this->team->addMember($this->user, Classes::WARDEN, Roles::HEALER);
        $this->assertTrue($this->team->isMember($this->user));
        $this->assertEquals(Classes::WARDEN, $this->team->user($this->user)->class_id);
        $this->assertEquals(Roles::HEALER, $this->team->user($this->user)->role_id);

        $this->team->updateMember($this->user, Classes::NIGHTBLADE, Roles::MAGICKA_DD, ['Roar of Alkosh']);
        $this->assertEquals(Classes::NIGHTBLADE, $this->team->user($this->user)->class_id);
        $this->assertEquals(Roles::MAGICKA_DD, $this->team->user($this->user)->role_id);
        $this->assertContains('Roar of Alkosh', json_decode($this->team->user($this->user)->sets, true));
    }

    /** @test */
    public function removeMemberTest()
    {
        $this->team->addMember($this->user, Classes::WARDEN, Roles::HEALER);
        $this->assertTrue($this->team->isMember($this->user));
        $this->team->removeMember($this->user);
        $this->assertFalse($this->team->isMember($this->user));
    }
}