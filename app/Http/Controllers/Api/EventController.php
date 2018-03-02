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

namespace App\Http\Controllers\Api;

use App\Character;
use App\Event;
use App\Guild;
use App\Hook;
use App\LogEntry;
use App\Signup;
use App\User;
use DateTime;
use DateTimeZone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends ApiController
{
    /**
     * Create an event.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        /** @var Guild $guild */
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
            if ($hook->matchesEventTags($event)) {
                $hook->call($event);
            }
        }

        return response(null, 200);
    }

    /**
     * Modify an existing event.
     *
     * @param Request $request
     * @param int     $event_id
     *
     * @return JsonResponse
     */
    public function edit(Request $request, int $event_id): JsonResponse
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $event = Event::query()->find($event_id);

        /** @var Guild $guild */
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

        return response(null, 200);
    }

    /**
     * Delete an event.
     *
     * @param Request $request
     * @param int     $event_id
     *
     * @return JsonResponse
     */
    public function delete(Request $request, int $event_id): JsonResponse
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $event = Event::query()->find($event_id);

        /** @var Guild $guild */
        $guild = Guild::query()->find($event->guild_id);

        if (!$guild->isAdmin($user->id)) {
            return response(null, 401);
        }

        Signup::query()->where('event_id', '=', $event->id)->delete();

        $log = new LogEntry();
        $log->create($guild->id, $user->name.' deleted the event '.$event->name.'. (Via App)');

        $event->delete();

        return response(null, 200);
    }

    /**
     * Get all signups of an event.
     *
     * @param Request $request
     * @param int     $event_id
     *
     * @return JsonResponse
     */
    public function allSignups(Request $request, int $event_id): JsonResponse
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        return response(Signup::query()->where('event_id', '=', $event_id)->orderBy('created_at')->get() ?? [], 200);
    }

    /**
     * Get the signup of an event for the user (self only).
     *
     * @param Request $request
     * @param int     $event_id
     *
     * @return JsonResponse
     */
    public function getSignup(Request $request, int $event_id): JsonResponse
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        return response(Signup::query()
                ->where('event_id', '=', $event_id)
                ->where('user_id', '=', $user->id)
                ->first() ?? [], 200);
    }

    /**
     * Sign up for an event (self).
     *
     * @param Request $request
     * @param int     $event_id
     *
     * @return JsonResponse
     */
    public function createSignup(Request $request, int $event_id): JsonResponse
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $event = Event::query()->find($event_id);

        /** @var Guild $guild */
        $guild = Guild::query()->find($event->guild_id);

        if (!$guild->isMember($user->id) || 1 === $event->locked) {
            return response(null, 401);
        }

        $signup           = new Signup();
        $signup->event_id = $event->id;
        $signup->user_id  = $user->id;

        Signup::query()->where('event_id', '=', $event_id)->where('user_id', '=', $user->id)->delete();

        if (!empty($request->input('character'))) {
            if (0 === $request->input('character')) {
                return response('Bad character parameter', 400);
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

        return response(null, 200);
    }

    /**
     * Edit a signup (self).
     *
     * @param Request $request
     * @param int     $event_id
     *
     * @return JsonResponse
     */
    public function editSignup(Request $request, int $event_id): JsonResponse
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $event = Event::query()->find($event_id);

        /** @var Guild $guild */
        $guild = Guild::query()->find($event->guild_id);

        if (!$guild->isMember($user->id) || 1 === $event->locked) {
            return response(null, 401);
        }

        $signup = Signup::query()->where('user_id', '=', $user->id)->where('event_id', '=', $event_id)->first();

        if (!empty($request->input('character'))) {
            if (0 === $request->input('character')) {
                return response('Bad character parameter', 400);
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

        return response(null, 200);
    }

    /**
     * Sign the user off for an event (self).
     *
     * @param Request $request
     * @param int     $event_id
     *
     * @return JsonResponse
     */
    public function deleteSignup(Request $request, int $event_id): JsonResponse
    {
        $user = $this->login($request);
        if (false === $user) {
            return response(null, 401);
        }

        $event = Event::query()->find($event_id);

        /** @var Guild $guild */
        $guild = Guild::query()->find($event->guild_id);

        if (1 === $event->locked || !$guild->isMember($user->id)) {
            return response(null, 401);
        }

        Signup::query()->where('event_id', '=', $event_id)->where('user_id', '=', $user->id)->delete();

        $log = new LogEntry();
        $log->create($guild->id, $user->name.' signed off for <a href="/g/'.$guild->slug.'/event/'.$event->id.'">'.$event->name.'</a>. (Via App)');

        return response(null, 200);
    }

    /**
     * Change the status of a signup to confirmed.
     *
     * @param Request $request
     * @param int     $signup_id
     *
     * @return JsonResponse
     */
    public function confirmSignup(Request $request, int $signup_id): JsonResponse
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $signup = Signup::query()->find($signup_id);

        $event = Event::query()->find($signup->event_id);

        /** @var Guild $guild */
        $guild = Guild::query()->find($event->guild_id);

        if (!$guild->isAdmin($user->id)) {
            return response(null, 401);
        }

        $signup->status = 1;
        $signup->save();

        return response(null, 200);
    }

    /**
     * Change the status of a signup to backup.
     *
     * @param Request $request
     * @param int     $signup_id
     *
     * @return JsonResponse
     */
    public function backupSignup(Request $request, int $signup_id): JsonResponse
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $signup = Signup::query()->find($signup_id);

        $event = Event::query()->find($signup->event_id);

        /** @var Guild $guild */
        $guild = Guild::query()->find($event->guild_id);

        if (!$guild->isAdmin($user->id)) {
            return response(null, 401);
        }

        $signup->status = 2;
        $signup->save();

        return response(null, 200);
    }

    /**
     * Sign off a user for an event by admin action.
     *
     * @param Request $request
     * @param int     $signup_id
     *
     * @return JsonResponse
     */
    public function deleteSignupOther(Request $request, int $signup_id): JsonResponse
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $signup = Signup::query()->find($signup_id);

        $u2 = User::query()->find($signup->user_id);

        $event = Event::query()->find($signup->event_id);

        /** @var Guild $guild */
        $guild = Guild::query()->find($event->guild_id);

        if (!$guild->isAdmin($user->id)) {
            return response(null, 401);
        }

        $signup->delete();

        $log = new LogEntry();
        $log->create($guild->id, $user->name.' signed off '.$u2->name.' for <a href="/g/'.$guild->slug.'/event/'.$event->id.'">'.$event->name.'</a>. (Via App)');

        return response(null, 200);
    }
}
