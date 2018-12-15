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
 * Time: 17:56
 */

namespace Tests\Feature\Model;


use App\Event;
use App\Guild;
use App\Team;
use App\User;
use App\Utility\Classes;
use App\Utility\Roles;
use Tests\TestCase;

class EventTest extends TestCase
{
    /**
     * @var Guild
     */
    protected $guild;

    /**
     * @var User
     */
    protected $signedUpUser;

    /**
     * @var User
     */
    protected $notSignedUpUser;

    /**
     * @var Event
     */
    protected $event;

    public function setUp()
    {
        parent::setUp();

        $this->artisan('migrate:refresh', [
            '--seed' => true,
        ]);

        $this->guild = Guild::query()->find(1);
        $this->signedUpUser = User::query()->find(1);
        $this->notSignedUpUser = User::query()->find(2);
        $this->event = Event::query()->find(1);
    }

    /** @test */
    public function signupTest()
    {
        $this->assertFalse($this->event->isSignedUp($this->notSignedUpUser));
        $this->event->signup($this->notSignedUpUser, Classes::TEMPLAR, Roles::MAGICKA_DD, ['Swamp Raider']);
        $this->assertTrue($this->event->isSignedUp($this->notSignedUpUser));
        $signup = $this->event->signups()->where('user_id', '=', $this->notSignedUpUser->id)->first();
        $this->assertEquals(Classes::TEMPLAR, $signup->class_id);
        $this->assertEquals(Roles::MAGICKA_DD, $signup->role_id);
        $this->assertContains('Swamp Raider', $signup->getSets());

        $created = $signup->created_at;
        sleep(1);

        // Testing editing signup
        $this->event->signup($this->notSignedUpUser, Classes::DRAGONKNIGHT, Roles::TANK, ['Ebon Armory']);
        $this->assertTrue($this->event->isSignedUp($this->notSignedUpUser));
        $signup = $this->event->signups()->where('user_id', '=', $this->notSignedUpUser->id)->first();
        $this->assertEquals(Classes::DRAGONKNIGHT, $signup->class_id);
        $this->assertEquals(Roles::TANK, $signup->role_id);
        $this->assertContains('Ebon Armory', $signup->getSets());
        $this->assertEquals($created, $signup->created_at);
    }

    /** @test */
    public function signupWithCharacterTest()
    {
        $character = $this->notSignedUpUser->characters()->find(1);
        $this->assertFalse($this->event->isSignedUp($this->notSignedUpUser));
        $this->event->signupWithCharacter($this->notSignedUpUser, $character);
        $this->assertTrue($this->event->isSignedUp($this->notSignedUpUser));
        $signup = $this->event->signups()->where('user_id', '=', $this->notSignedUpUser->id)->first();
        $this->assertEquals($character->class, $signup->class_id);
        $this->assertEquals($character->role, $signup->role_id);
        $this->assertEquals($character->sets(), $signup->getSets());

        $created = $signup->created_at;
        sleep(1);

        $character = $this->notSignedUpUser->characters()->find(2);
        $this->event->signupWithCharacter($this->notSignedUpUser, $character);
        $this->assertTrue($this->event->isSignedUp($this->notSignedUpUser));
        $signup = $this->event->signups()->where('user_id', '=', $this->notSignedUpUser->id)->first();
        $this->assertEquals($character->class, $signup->class_id);
        $this->assertEquals($character->role, $signup->role_id);
        $this->assertEquals($character->sets(), $signup->getSets());
        $this->assertEquals($created, $signup->created_at);
    }

    /** @test */
    public function signupTeamTest()
    {
        /** @var Team $team */
        $team = Team::query()->find(1);
        $team->addMember($this->notSignedUpUser, Classes::NIGHTBLADE, Roles::MAGICKA_DD);
        $this->event->signupWithTeam($team);
        $this->assertTrue($this->event->isSignedUp($this->notSignedUpUser));
    }

    /** @test */
    public function lockTest()
    {
        $this->assertFalse($this->event->locked());
        $this->event->lock(Event::EVENT_STATUS_LOCKED);
        $this->assertTrue($this->event->locked());
        $this->event->lock(Event::EVENT_STATUS_OPEN);
        $this->assertFalse($this->event->locked());
    }

    /** @test */
    public function addCommentTest()
    {
        $this->assertCount(0, $this->event->comments());
        $this->event->addComment($this->notSignedUpUser, 'Test Comment');
        $this->assertCount(1, $this->event->comments());
    }

    /** @test */
    public function removeCommentTest()
    {
        $this->assertCount(0, $this->event->comments());
        $this->event->addComment($this->notSignedUpUser, 'Test Comment');
        $this->assertCount(1, $this->event->comments());
        $this->event->deleteComment($this->event->comments()[0]);
        $this->assertCount(0, $this->event->comments());
    }
}