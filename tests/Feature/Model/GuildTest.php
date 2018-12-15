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
 * Date: 14.09.18
 * Time: 14:26
 */

namespace Tests\Feature\Model;


use App\Guild;
use App\User;
use Tests\TestCase;

class GuildTest extends TestCase
{
    /**
     * @var Guild
     */
    protected $guild;

    /**
     * @var User
     */
    protected $owner;

    /**
     * @var User
     */
    protected $member;

    /**
     * @var User
     */
    protected $nonMember;

    public function setUp()
    {
        parent::setUp();

        $this->artisan('migrate:refresh', [
            '--seed' => true,
        ]);

        $this->guild = Guild::query()->find(1);
        $this->owner = User::query()->find(1);
        $this->member = User::query()->find(2);
        $this->nonMember = User::query()->find(3);
    }

    /** @test */
    public function guildMemberTest()
    {
        $this->assertTrue($this->guild->isMember($this->member));
        $this->assertFalse($this->guild->isMember($this->nonMember));
    }

    /** @test */
    public function addMemberTest()
    {
        $this->assertFalse($this->guild->isMember($this->nonMember));
        $this->guild->applyMember($this->nonMember);
        $this->assertTrue($this->guild->isPendingMember($this->nonMember));
        $this->assertFalse($this->guild->isMember($this->nonMember));
        $this->guild->approveMember($this->nonMember);
        $this->assertTrue($this->guild->isMember($this->nonMember));
    }

    /** @test */
    public function removeMemberTest()
    {
        $this->assertTrue($this->guild->isMember($this->member));
        $this->guild->removeMember($this->member);
        $this->assertFalse($this->guild->isMember($this->member));
    }

    /** @test */
    public function addAdminTest()
    {
        $this->assertFalse($this->guild->isAdmin($this->member));
        $this->guild->addAdmin($this->member);
        $this->assertTrue($this->guild->isAdmin($this->member));
    }

    /** @test */
    public function removeAdminTest()
    {
        $this->assertFalse($this->guild->isAdmin($this->member));
        $this->guild->addAdmin($this->member);
        $this->assertTrue($this->guild->isAdmin($this->member));
        $this->guild->removeAdmin($this->member);
        $this->assertFalse($this->guild->isAdmin($this->member));
    }

    /** @test */
    public function transferOwnershipTest()
    {
        $this->assertEquals(1, $this->guild->owner_id);
        $this->guild->transferOwnership($this->member);
        $this->assertEquals($this->member->id, $this->guild->owner_id);
        $this->assertTrue($this->guild->isAdmin($this->member));
    }
}