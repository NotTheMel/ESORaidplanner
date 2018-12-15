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

namespace App\Http\Controllers\Api\UserBased;

use App\Character;
use App\Event;
use App\Guild;
use App\Signup;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EventController extends UserApiController
{
    public function create(Request $request)
    {
        /** @var Guild $guild */
        $guild = Guild::query()->find($request->input('guild_id'));

        $event = $guild->createEvent(
            $request->input('name'),
            new \DateTime($request->input('start_date'), new DateTimeZone(env('DEFAULT_TIMEZONE'))),
            $request->input('description') ?? ''
        );

        $event->sendCreationNotifications();

        return response($event, Response::HTTP_CREATED);
    }

    public function update(Request $request, int $event_id)
    {
        /** @var Guild $guild */
        $guild = Guild::query()->find($request->input('guild_id'));
        $event = $guild->updateEvent(
            Event::query()->find($event_id),
            $request->input('name'),
            new \DateTime($request->input('start_date'), new DateTimeZone(env('DEFAULT_TIMEZONE'))),
            $request->input('description') ?? ''
        );

        return \response($event, Response::HTTP_OK);
    }

    public function delete(Request $request, int $event_id)
    {
        $event = Event::query()->find($event_id);
        $event->delete();

        return \response(null, Response::HTTP_OK);
    }

    public function get(Request $request, int $event_id)
    {
        $event = Event::query()->find($event_id);

        return \response($event, Response::HTTP_OK);
    }

    public function getSignups(Request $request, int $event_id)
    {
        /** @var Event $event */
        $event = Event::query()->find($event_id);

        return \response($event->signups, Response::HTTP_OK);
    }

    public function signup(Request $request, int $event_id)
    {
        $user = $this->getRequestUser($request);

        /** @var Event $event */
        $event = Event::query()->find($event_id);
        if (!empty($request->input('character'))) {
            $character = Character::query()->find($request->input('character'));
            $event->signupWithCharacter($user, $character);
        } else {
            $event->signup($user, $request->input('class'), $request->input('role'), $request->input('sets') ?? []);
        }

        return \response(null, Response::HTTP_OK);
    }

    public function signoff(Request $request, int $event_id)
    {
        $user = $this->getRequestUser($request);

        /** @var Event $event */
        $event = Event::query()->find($event_id);
        $event->signoff($user);

        return \response(null, Response::HTTP_OK);
    }

    public function setSignupStatus(Request $request, int $signup_id, int $status)
    {
        /** @var Signup $signup */
        $signup = Signup::query()->find($signup_id);
        $signup->setStatus($status);

        return \response(null, Response::HTTP_OK);
    }
}
