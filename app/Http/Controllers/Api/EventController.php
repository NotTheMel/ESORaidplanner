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
use App\Hook\EventCreationNotification;
use App\LogEntry;
use App\Set;
use App\Signup;
use App\User;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;

class EventController extends ApiController
{
    /**
     * Create an event.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request): Response
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        /** @var Guild $guild */
        $guild = Guild::query()->find($request->input('guild_id'));

        if (!$guild->isAdmin($user)) {
            return response(null, Response::HTTP_UNAUTHORIZED);
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

        $hooks = EventCreationNotification::query()->where('call_type', '=', 1)->where('guild_id', '=', $guild->id)->get();

        foreach ($hooks as $hook) {
            if ($hook->matchesEventTags($event)) {
                $hook->call($event);
            }
        }

        return response(null, Response::HTTP_OK);
    }

    /**
     * Modify an existing event.
     *
     * @param Request $request
     * @param int     $event_id
     *
     * @return Response
     */
    public function edit(Request $request, int $event_id): Response
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        $event = Event::query()->find($event_id);

        /** @var Guild $guild */
        $guild = Guild::query()->find($event->guild_id);

        if (!$guild->isAdmin($user)) {
            return response(null, Response::HTTP_UNAUTHORIZED);
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

        return response(null, Response::HTTP_OK);
    }

    /**
     * Delete an event.
     *
     * @param Request $request
     * @param int     $event_id
     *
     * @return Response
     */
    public function delete(Request $request, int $event_id): Response
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        $event = Event::query()->find($event_id);

        /** @var Guild $guild */
        $guild = Guild::query()->find($event->guild_id);

        if (!$guild->isAdmin($user)) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        Signup::query()->where('event_id', '=', $event->id)->delete();

        $log = new LogEntry();
        $log->create($guild->id, $user->name.' deleted the event '.$event->name.'. (Via App)');

        $event->delete();

        return response(null, Response::HTTP_OK);
    }

    /**
     * Get all signups of an event.
     *
     * @param Request $request
     * @param int     $event_id
     *
     * @return Response
     */
    public function allSignups(Request $request, int $event_id): Response
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        return response(Signup::query()->where('event_id', '=', $event_id)->orderBy('created_at')->get() ?? [], Response::HTTP_OK);
    }

    /**
     * Get the signup of an event for the user (self only).
     *
     * @param Request $request
     * @param int     $event_id
     *
     * @return Response
     */
    public function getSignup(Request $request, int $event_id): Response
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        return response(Signup::query()
                ->where('event_id', '=', $event_id)
                ->where('user_id', '=', $user->id)
                ->first() ?? [], Response::HTTP_OK);
    }

    /**
     * Sign up for an event (self).
     *
     * @param Request $request
     * @param int     $event_id
     *
     * @return Response
     */
    public function createSignup(Request $request, int $event_id): Response
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        /** @var Event $event */
        $event = Event::query()->find($event_id);

        /** @var Guild $guild */
        $guild = Guild::query()->find($event->guild_id);

        if (!$guild->isMember($user) || 1 === $event->locked) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        if (!empty(Input::get('character'))) {
            $character = Character::query()->find(Input::get('character'));
            $event->signup($user, null, null, [], $character);
        } else {
            $event->signup($user, $request->input('role'), $request->input('class'), Set::Array($request->input('sets') ?? []));
        }

        return response(null, Response::HTTP_OK);
    }

    /**
     * Edit a signup (self).
     *
     * @param Request $request
     * @param int     $event_id
     *
     * @return Response
     */
    public function editSignup(Request $request, int $event_id): Response
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        /** @var Event $event */
        $event = Event::query()->find($event_id);

        /** @var Guild $guild */
        $guild = Guild::query()->find($event->guild_id);

        if (!$guild->isMember($user) || 1 === $event->locked) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        if (!empty(Input::get('character'))) {
            $character = Character::query()->find(Input::get('character'));
            $event->editSignup($user, null, null, [], $character);
        } else {
            $event->editSignup($user, $request->input('role'), $request->input('class'), Set::Array($request->input('sets') ?? []));
        }

        return response(null, Response::HTTP_OK);
    }

    /**
     * Sign the user off for an event (self).
     *
     * @param Request $request
     * @param int     $event_id
     *
     * @return Response
     */
    public function deleteSignup(Request $request, int $event_id): Response
    {
        $user = $this->login($request);
        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        /** @var Event $event */
        $event = Event::query()->find($event_id);

        /** @var Guild $guild */
        $guild = Guild::query()->find($event->guild_id);

        if (1 === $event->locked || !$guild->isMember($user)) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        $event->signoff($user);

        return response(null, Response::HTTP_OK);
    }

    /**
     * Change the status of a signup to confirmed.
     *
     * @param Request $request
     * @param int     $signup_id
     *
     * @return Response
     */
    public function confirmSignup(Request $request, int $signup_id): Response
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        /** @var Event $event */
        $event = Event::query()->find(Signup::query()->find($signup_id)->event_id);

        /** @var Guild $guild */
        $guild = Guild::query()->find($event->guild_id);

        if (!$guild->isAdmin($user)) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        $event->setSignupStatus($signup_id, Signup::SIGNUP_STATUS_CONFIRMED);

        return response(null, Response::HTTP_OK);
    }

    /**
     * Change the status of a signup to backup.
     *
     * @param Request $request
     * @param int     $signup_id
     *
     * @return Response
     */
    public function backupSignup(Request $request, int $signup_id): Response
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        /** @var Event $event */
        $event = Event::query()->find(Signup::query()->find($signup_id)->event_id);

        /** @var Guild $guild */
        $guild = Guild::query()->find($event->guild_id);

        if (!$guild->isAdmin($user)) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        $event->setSignupStatus($signup_id, Signup::SIGNUP_STATUS_BACKUP);

        return response(null, Response::HTTP_OK);
    }

    /**
     * Sign off a user for an event by admin action.
     *
     * @param Request $request
     * @param int     $signup_id
     *
     * @return Response
     */
    public function deleteSignupOther(Request $request, int $signup_id): Response
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        $signup = Signup::query()->find($signup_id);

        $u2 = User::query()->find($signup->user_id);

        /** @var Event $event */
        $event = Event::query()->find($signup->event_id);

        /** @var Guild $guild */
        $guild = Guild::query()->find($event->guild_id);

        if (!$guild->isAdmin($user)) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        $event->signoffOther($u2, $user);

        return response(null, Response::HTTP_OK);
    }
}
