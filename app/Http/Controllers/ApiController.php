<?php

/**
 * This file is part of the ESO Raidplanner project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/ESORaidplanner/ESORaidplanner
 */

namespace App\Http\Controllers;

use App\Character;
use App\Event;
use App\Guild;
use App\Hook;
use App\LogEntry;
use App\Set;
use App\Signup;
use App\User;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    /**
     * Simple true/false login check.
     *
     * @param Request $request
     *
     * @return array|bool
     */
    public function checkLogin(Request $request)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $u = [];

        $u['name']   = $user->name;
        $u['email']  = $user->email;
        $u['layout'] = $user->layout;

        return $u;
    }

    public function createUser(Request $request)
    {
        if ($request->input('password') !== $request->input('password_repeat')) {
            return response('Passwords do not match.', 400);
        }
        if (!filter_var($request->input('email'), FILTER_VALIDATE_EMAIL)) {
            return response('Email address is not a valid email address.', 400);
        }
        if (User::query()->where('email', '=', $request->input('email'))->count() > 0) {
            return response('A user with that email address already exists.', 400);
        }
        if (!array_key_exists($request->input('timezone'), $this->getTimezones())) {
            return response('Invalid timezone.', 400);
        }

        $user              =  new User();
        $user->email       = $request->input('email');
        $user->name        = $request->input('name');
        $user->password    = bcrypt($request->input('password'));
        $user->timezone    = $request->input('timezone');
        $user->description = '';

        $user->save();

        return User::query()->where('email', '=', $request->input('email'))->first();
    }

    /**
     * Get the current user.
     *
     * @param Request $request
     *
     * @return array|bool|mixed
     */
    public function getUser(Request $request)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        return $user;
    }

    /**
     * Get limited information about a user.
     *
     * @param Request $request
     * @param int     $user_id
     *
     * @return User|array
     */
    public function getUserInfo(Request $request, int $user_id)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $u = DB::table('users')
            ->select('id', 'name', 'avatar')
            ->where('id', '=', $user_id)
            ->first();

        $u1         = new User();
        $u1->avatar = $u->avatar;
        $u1->name   = $u->name;
        $u1->id     = $u->id;

        return $u1;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function addOnesignalIdToUser(Request $request)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $device_id    = $request->input('device');
        $onesignal_id = $request->input('onesignal_id');

        if (!empty($device_id) && !empty($onesignal_id)) {
            $user->addOnesignalId($device_id, $onesignal_id);
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function removeOnesignalIdFromUser(Request $request)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $device_id = $request->input('device');

        if (!empty($device_id)) {
            $user->removeOnesignalId($device_id);
        }
    }

    /**
     * Get a guild, but only if the user is a member.
     *
     * @param Request $request
     * @param int     $guild_id
     *
     * @return array|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function getGuild(Request $request, int $guild_id)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $count = DB::table('user_guilds')->where('user_id', '=', $user->id)->where('guild_id', '=', $guild_id)->count();

        if (0 === $count) {
            return response(null, 401);
        }

        return Guild::query()->find($guild_id);
    }

    /**
     * Get all guilds of the user (self).
     *
     * @param Request $request
     *
     * @return array
     */
    public function getUserGuilds(Request $request)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $ids = DB::table('user_guilds')
            ->where('user_id', '=', $user->id)
            ->where('status', '>', 0)
            ->get();

        $guilds = [];

        foreach ($ids as $id) {
            $guild = Guild::query()->find($id->guild_id);

            $count = DB::table('user_guilds')->where('guild_id', '=', $guild->id)->where('status', '>', 0)->count();

            $guild->member_count = $count;

            array_push($guilds, $guild);
        }

        return $guilds;
    }

    /**
     * @param Request $request
     *
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getUserGuildsPending(Request $request)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $ids = DB::table('user_guilds')
            ->where('user_id', '=', $user->id)
            ->where('status', '=', 0)
            ->get();

        $guilds = [];

        foreach ($ids as $id) {
            $guilds[] = Guild::query()->find($id->guild_id);
        }

        return $guilds;
    }

    /**
     * @param Request $request
     * @param int     $guild_id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response|void
     */
    public function requestGuildMembership(Request $request, int $guild_id)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $count = DB::table('user_guilds')
            ->where('user_id', '=', $user->id)
            ->where('guild_id', '=', $guild_id)
            ->count();

        if ($count > 0) {
            return;
        }

        DB::table('user_guilds')->insert([
            'user_id'  => $user->id,
            'guild_id' => $guild_id,
            'status'   => 0,
        ]);

        $log = new LogEntry();
        $log->create($guild_id, $user->name.' requested membership. (Via App)');
    }

    /**
     * @param Request $request
     * @param int     $guild_id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function leaveGuild(Request $request, int $guild_id)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $guild = Guild::query()->find($guild_id);

        if ($guild->isOwner($user->id)) {
            return response(null, 401);
        }

        if($guild->isAdmin($user->id)) {
            $guild->removeAdmin($user->id, $user->id);
        }

        DB::table('user_guilds')->where('user_id', '=', $user->id)->where('guild_id', '=', $guild->id)->delete();

        $log = new LogEntry();
        $log->create($guild->id, $user->name.' left the guild. (Via App)');
    }

    /**
     * @param Request $request
     * @param int     $guild_id
     * @param int     $user_id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function approveGuildMembership(Request $request, int $guild_id, int $user_id)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $guild = Guild::query()->find($guild_id);

        if (!$guild->isAdmin($user->id)) {
            return response(null, 401);
        }

        DB::table('user_guilds')->where('user_id', '=', $user_id)->where('guild_id', '=', $guild_id)->update(['status' => 1]);

        $user_1 = User::query()->find($user_id);

        $log = new LogEntry();
        $log->create($guild->id, $user->name.' approved the membership request of '.$user_1->name.'. (Via App)');
    }

    /**
     * @param Request $request
     * @param int     $guild_id
     * @param int     $user_id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function removeGuildMembership(Request $request, int $guild_id, int $user_id)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $guild = Guild::query()->find($guild_id);

        if (!$guild->isAdmin($user->id) || $guild->owner_id === $user_id) {
            return response(null, 401);
        }

        DB::table('user_guilds')->where('user_id', '=', $user_id)->where('guild_id', '=', $guild_id)->delete();

        $guild->removeAdmin($user_id, $user->id);

        $user_1 = User::query()->find($user_id);

        $log = new LogEntry();
        $log->create($guild->id, $user->name.' removed '.$user_1->name.' from the guild. (Via App)');
    }

    /**
     * @param Request $request
     * @param int     $guild_id
     * @param int     $user_id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function makeGuildAdmin(Request $request, int $guild_id, int $user_id)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $guild = Guild::query()->find($guild_id);

        if ($guild->owner_id !== $user->id) {
            return response(null, 401);
        }

        if (!$guild->isAdmin($user_id)) {
            $guild->makeAdmin($user_id, $user->id);

            $u = User::query()->find($user_id);

            $log = new LogEntry();
            $log->create($guild->id, $user->name.' promoted '.$u->name.' to admin.');
        }
    }

    /**
     * @param Request $request
     * @param int     $guild_id
     * @param int     $user_id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function removeGuildAdmin(Request $request, int $guild_id, int $user_id)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $guild = Guild::query()->find($guild_id);

        if ($guild->owner_id !== $user->id || $guild->owner_id === $user_id) {
            return response(null, 401);
        }

        if ($guild->isAdmin($user_id)) {
            $guild->removeAdmin($user_id, $user->id);

            $u = User::query()->find($user_id);

            $log = new LogEntry();
            $log->create($guild->id, $user->name.' demoted '.$u->name.' to member.');
        }
    }

    /**
     * Get the signup of an event for the user (self only).
     *
     * @param Request $request
     * @param int     $event_id
     *
     * @return array|\Illuminate\Database\Eloquent\Model|null|static
     */
    public function getSignup(Request $request, int $event_id)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        return Signup::query()
                ->where('event_id', '=', $event_id)
                ->where('user_id', '=', $user->id)
                ->first() ?? [];
    }

    /**
     * Get all events for the user (self).
     *
     * @param Request $request
     *
     * @return array
     */
    public function getUserEvents(Request $request)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $events = [];

        foreach ($this->getUserGuilds($request) as $guild) {
            $e = Event::query()->where('guild_id', '=', $guild->id)->get();

            foreach ($e as $a) {
                $date = new DateTime($a->start_date);

                $date->setTimezone(new DateTimeZone('UTC'));

                $a->start_date = $date->format('Y-m-d H:i:s');

                $events[] = $a;
            }
        }

        return $events;
    }

    /**
     * Get all events the user is signed up for (self).
     *
     * @param Request $request
     *
     * @return array
     */
    public function getUserEventsSignedUp(Request $request)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $events = $this->getUserEvents($request);

        $e = [];

        foreach ($events as $event) {
            if ($event->userIsSignedUp($user->id)) {
                $e[] = $event;
            }
        }

        return $e;
    }

    /**
     * Get all signups for the user (self).
     *
     * @param Request $request
     *
     * @return array
     */
    public function getUserSignups(Request $request)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $events = $this->getUserEventsSignedUp($request);

        if (0 === count($events)) {
            return [];
        }

        $signups = [];

        foreach ($events as $event) {
            $signups[] = Signup::query()
                ->where('user_id', '=', $user->id)
                ->where('event_id', '=', $event->id)
                ->first();
        }

        return $signups;
    }

    /**
     * Get all upcoming events for a guild.
     *
     * @param Request $request
     *
     * @return array
     */
    public function getGuildEvents(Request $request, int $guild_id)
    {
        $user = $this->login($request);

        $guild = Guild::query()->find($guild_id);

        if (false === $user || !$guild->isMember($user->id)) {
            return response(null, 401);
        }

        $events = Event::query()
            ->where('guild_id', '=', $guild->id)
            ->where('start_date', '>', date('Y-m-d H:i:s'))
            ->orderBy('start_date', 'asc')
            ->get();

        $e = [];

        foreach ($events as $event) {
            $date = new DateTime($event->start_date);

            $date->setTimezone(new DateTimeZone('UTC'));

            $event->start_date = $date->format('Y-m-d H:i:s');

            $e[] = $event;
        }

        return $e ?? [];
    }

    /**
     * Get all members of a guild (limited user information).
     *
     * @param Request $request
     * @param int     $guild_id
     *
     * @return array
     */
    public function getGuildMembers(Request $request, int $guild_id)
    {
        $user = $this->login($request);

        $guild = Guild::query()->find($guild_id);

        if (false === $user || !$guild->isMember($user->id)) {
            return response(null, 401);
        }

        $members = DB::table('user_guilds')
            ->select('users.id as user_id', 'users.name as name', 'users.avatar as avatar', 'user_guilds.guild_id as guild_id', 'user_guilds.status as status')
            ->join('users', 'user_guilds.user_id', 'users.id')
            ->where('user_guilds.guild_id', '=', $guild->id)
            ->where('user_guilds.status', '>=', 1)
            ->orderBy('users.name', 'asc')
            ->get();

        return $members ?? [];
    }

    /**
     * @param Request $request
     * @param int     $guild_id
     *
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getGuildMembersPending(Request $request, int $guild_id)
    {
        $user = $this->login($request);

        $guild = Guild::query()->find($guild_id);

        if (false === $user || !$guild->isAdmin($user->id)) {
            return response(null, 401);
        }

        $members = DB::table('user_guilds')
            ->select('users.id as user_id', 'users.name as name', 'users.avatar as avatar', 'user_guilds.guild_id as guild_id', 'user_guilds.status as status')
            ->join('users', 'user_guilds.user_id', 'users.id')
            ->where('user_guilds.guild_id', '=', $guild->id)
            ->where('user_guilds.status', '=', 0)
            ->orderBy('users.name', 'asc')
            ->get();

        return $members ?? [];
    }

    /**
     * Create an event.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response|void
     */
    public function createEvent(Request $request)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $guild = Guild::query()->find($request->input('guild_id'));

        if (!$guild->isAdmin($user->id)) {
            return response(null, 401);
        }

        $event              = new Event();
        $event->name        = $request->input('name');
        $event->guild_id    = $guild->id;
        $event->type        = $request->input('type');
        $event->description = $request->input('description') ?? '';

        $dt = new DateTime($request->input('start_date'), new DateTimeZone('UTC'));
        $dt->setTimezone(new DateTimeZone('Europe/Amsterdam'));

        $event->start_date = $dt->format('Y-m-d H:i:s');

        $event->save();

        $log = new LogEntry();
        $log->create($guild->id, $user->name.' created the event '.$event->name.'. (Via App)');

        $hooks = Hook::query()->where('call_type', '=', 1)->where('guild_id', '=', $guild->id)->get();

        foreach ($hooks as $hook) {
            $hook->call($event);
        }
    }

    /**
     * Modify an existing event.
     *
     * @param Request $request
     * @param int     $event_id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response|void
     */
    public function modifyEvent(Request $request, int $event_id)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $event = Event::query()->find($event_id);

        $guild = Guild::query()->find($event->guild_id);

        if (!$guild->isAdmin($user->id)) {
            return response(null, 401);
        }

        $event->name        = $request->input('name') ?? $event->name;
        $event->type        = $request->input('type') ?? $event->type;
        $event->description = $request->input('description') ?? $event->description;

        if (!empty($request->input('start_date'))) {
            $dt = new DateTime($request->input('start_date'), new DateTimeZone('UTC'));
            $dt->setTimezone(new DateTimeZone('Europe/Amsterdam'));

            $event->start_date = $dt->format('Y-m-d H:i:s');
        }

        $event->save();
    }

    /**
     * Delete an event.
     *
     * @param Request $request
     * @param int     $event_id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteEvent(Request $request, int $event_id)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $event = Event::query()->find($event_id);

        $guild = Guild::query()->find($event->guild_id);

        if (!$guild->isAdmin($user->id)) {
            return response(null, 401);
        }

        Signup::query()->where('event_id', '=', $event->id)->delete();

        $log = new LogEntry();
        $log->create($guild->id, $user->name.' deleted the event '.$event->name.'. (Via App)');

        $event->delete();
    }

    /**
     * Get all signups of an event.
     *
     * @param Request $request
     * @param int     $event_id
     *
     * @return array
     */
    public function getEventSignups(Request $request, int $event_id)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        return Signup::query()->where('event_id', '=', $event_id)->orderBy('created_at')->get() ?? [];
    }

    /**
     * Sign up for an event (self).
     *
     * @param Request $request
     * @param int     $event_id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response|void
     */
    public function signUpUser(Request $request, int $event_id)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $event = Event::query()->find($event_id);

        $guild = Guild::query()->find($event->guild_id);

        if (!$guild->isMember($user->id)) {
            return response(null, 401);
        }

        $signup           = new Signup();
        $signup->event_id = $event->id;
        $signup->user_id  = $user->id;

        Signup::query()->where('event_id', '=', $event_id)->where('user_id', '=', $user->id)->delete();

        if (!empty($request->input('character'))) {
            if (0 == $request->input('character')) {
                return;
            }

            $character = Character::query()
                ->find($request->input('character'));
            $signup->class_id     = $character->class;
            $signup->role_id      = $character->role;
            $signup->sets         = $character->sets;
            $signup->character_id = $character->id;
        } else {
            $signup->class_id = $request->input('class');
            $signup->role_id  = $request->input('role');

            if (!empty($request->input('sets'))) {
                $signup->sets = $request->input('sets');
            } else {
                $signup->sets = '';
            }
        }

        $signup->save();

        $log = new LogEntry();
        $log->create($guild->id, $user->name.' signed up for <a href="/g/'.$guild->slug.'/event/'.$event->id.'">'.$event->name.'</a>. (Via App)');
    }

    /**
     * Edit a signup (self).
     *
     * @param Request $request
     * @param int     $event_id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response|void
     */
    public function editSignup(Request $request, int $event_id)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $event = Event::query()->find($event_id);

        $guild = Guild::query()->find($event->guild_id);

        if (!$guild->isMember($user->id)) {
            return response(null, 401);
        }

        $signup = Signup::query()->where('user_id', '=', $user->id)->where('event_id', '=', $event_id)->first();

        if (!empty($request->input('character'))) {
            if (0 == $request->input('character')) {
                return;
            }

            $character = Character::query()
                ->find($request->input('character'));
            $signup->class_id     = $character->class;
            $signup->role_id      = $character->role;
            $signup->sets         = $character->sets;
            $signup->character_id = $character->id;
        } else {
            $signup->class_id     = $request->input('class');
            $signup->role_id      = $request->input('role');
            $signup->character_id = null;

            if (!empty($request->input('sets'))) {
                $signup->sets = $request->input('sets');
            } else {
                $signup->sets = '';
            }
        }

        $signup->save();
    }

    /**
     * Sign the user off for an event (self).
     *
     * @param Request $request
     * @param int     $event_id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function signOffUser(Request $request, int $event_id)
    {
        $user = $this->login($request);
        if (false === $user) {
            return response(null, 401);
        }

        $event = Event::query()->find($event_id);

        $guild = Guild::query()->find($event->guild_id);

        if (!$guild->isMember($user->id)) {
            return response(null, 401);
        }

        Signup::query()->where('event_id', '=', $event_id)->where('user_id', '=', $user->id)->delete();

        $log = new LogEntry();
        $log->create($guild->id, $user->name.' signed off for <a href="/g/'.$guild->slug.'/event/'.$event->id.'">'.$event->name.'</a>. (Via App)');
    }

    /**
     * Change the status of a signup to confirmed.
     *
     * @param Request $request
     * @param int     $signup_id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function confirmSignup(Request $request, int $signup_id)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $signup = Signup::query()->find($signup_id);

        $event = Event::query()->find($signup->event_id);

        $guild = Guild::query()->find($event->guild_id);

        if (!$guild->isAdmin($user->id)) {
            return response(null, 401);
        }

        $signup->status = 1;
        $signup->save();
    }

    /**
     * Change the status of a signup to backup.
     *
     * @param Request $request
     * @param int     $signup_id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function backupSignup(Request $request, int $signup_id)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $signup = Signup::query()->find($signup_id);

        $event = Event::query()->find($signup->event_id);

        $guild = Guild::query()->find($event->guild_id);

        if (!$guild->isAdmin($user->id)) {
            return response(null, 401);
        }

        $signup->status = 2;
        $signup->save();
    }

    /**
     * Sign off a user for an event by admin action.
     *
     * @param Request $request
     * @param int     $signup_id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function removeSignup(Request $request, int $signup_id)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $signup = Signup::query()->find($signup_id);

        $u2 = User::query()->find($signup->user_id);

        $event = Event::query()->find($signup->event_id);

        $guild = Guild::query()->find($event->guild_id);

        if (!$guild->isAdmin($user->id)) {
            return response(null, 401);
        }

        $signup->delete();

        $log = new LogEntry();
        $log->create($guild->id, $user->name.' signed off '.$u2->name.' for <a href="/g/'.$guild->slug.'/event/'.$event->id.'">'.$event->name.'</a>. (Via App)');
    }

    /**
     * Create a character preset.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function createCharacter(Request $request)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $character          = new Character();
        $character->name    = $request->input('name');
        $character->class   = $request->input('class');
        $character->role    = $request->input('role');
        $character->user_id = $user->id;
        $character->public  = $request->input('public') ?? 0;

        if (!empty($request->input('sets'))) {
            $character->sets = $request->input('sets');
        } else {
            $character->sets = '';
        }

        $character->save();
    }

    /**
     * Modify an existing character preset.
     *
     * @param Request $request
     * @param int     $character_id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response|void
     */
    public function modifycharacter(Request $request, int $character_id)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $character = Character::query()->find($character_id);

        if ($character->user_id !== $user->id) {
            return response(null, 401);
        }

        $character->name    = $request->input('name') ?? $character->name;
        $character->class   = $request->input('class') ?? $character->class;
        $character->role    = $request->input('role') ?? $character->role;
        $character->user_id = $user->id;
        $character->public  = $request->input('public') ?? $character->public;
        $character->sets    = $request->input('sets') ?? $character->sets;

        $character->save();

        $signups = $this->getUserSignups($request);

        foreach ($signups as $signup) {
            $event = Event::query()->find($signup->event_id);

            $dt  = new DateTime($event->start_date);
            $now = new DateTime();

            if ($signup->character_id === $character->id && $dt > $now) {
                $sign           = Signup::query()->find($signup->id);
                $sign->class_id = $character->class;
                $sign->role_id  = $character->role;
                $sign->sets     = $character->sets;
                $sign->save();
            }
        }
    }

    /**
     * Delete a character preset.
     *
     * @param Request $request
     * @param int     $character_id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteCharacter(Request $request, int $character_id)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $character = Character::query()->find($character_id);

        if ($character->user_id !== $user->id) {
            return response(null, 401);
        }

        $signups = $this->getUserSignups($request);

        foreach ($signups as $signup) {
            if ($signup->character_id === $character->id) {
                $sign               = Signup::query()->find($signup->id);
                $sign->character_id = null;
                $sign->save();
            }
        }

        $character->delete();
    }

    /**
     * Get all gear sets known in the application.
     *
     * @param Request $request
     *
     * @return array|\Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getSets(Request $request)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        return Set::query()->orderBy('name')->get();
    }

    /**
     * @return int
     */
    public function getSetsVersion(): int
    {
        $set = Set::query()->orderBy('version', 'desc')->first();

        return $set->version;
    }

    /**
     * Gets all guilds associated with the user.
     *
     * @param Request $request
     *
     * @return array|\Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getGuilds(Request $request)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        return Guild::query()->orderBy('name')->get();
    }

    /**
     * Gets all characters associated with the user.
     *
     * @param Request $request
     *
     * @return array|\Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getCharacters(Request $request)
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        return Character::query()->where('user_id', '=', $user->id)->orderBy('name')->get() ?? [];
    }

    /**
     * Return list of supported timezones.
     *
     * @return array
     */
    public function getTimezones(): array
    {
        static $regions = [
            DateTimeZone::AFRICA,
            DateTimeZone::AMERICA,
            DateTimeZone::ANTARCTICA,
            DateTimeZone::ASIA,
            DateTimeZone::ATLANTIC,
            DateTimeZone::AUSTRALIA,
            DateTimeZone::EUROPE,
            DateTimeZone::INDIAN,
            DateTimeZone::PACIFIC,
        ];

        $timezones = [];
        foreach ($regions as $region) {
            $timezones = array_merge($timezones, DateTimeZone::listIdentifiers($region));
        }

        $timezone_offsets = [];
        foreach ($timezones as $timezone) {
            $tz                          = new DateTimeZone($timezone);
            $timezone_offsets[$timezone] = $tz->getOffset(new DateTime());
        }

        // sort timezone by offset
        asort($timezone_offsets);

        $timezone_list = [];
        foreach ($timezone_offsets as $timezone => $offset) {
            $offset_prefix    = $offset < 0 ? '-' : '+';
            $offset_formatted = gmdate('H:i', abs($offset));

            $pretty_offset = "UTC${offset_prefix}${offset_formatted}";

            $timezone_list[$timezone] = "(${pretty_offset}) $timezone";
        }

        return $timezone_list;
    }

    /**
     * Checks the credentials of a request coming in over the API.
     *
     * @param Request $request
     *
     * @return bool|mixed
     */
    private function login(Request $request)
    {
        $header = str_replace('Basic ', '', $request->header('Authorization'));

        $header = explode(':', base64_decode($header), 2);

        $valid = Auth::validate(['email' => $header[0], 'password' => $header[1]]);

        if ($valid) {
            return User::query()->where('email', '=', $header[0])->first();
        }

        return false;
    }
}
