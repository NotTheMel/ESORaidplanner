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

use App\Guild;
use App\User;
use App\Utility\UserDateHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends UserApiController
{
    public function self(Request $request)
    {
        return response($this->getRequestUser($request), Response::HTTP_OK);
    }

    public function getPublicInfo(Request $request, int $user_id)
    {
        $u = User::query()
            ->select('id', 'name', 'avatar', 'timezone')
            ->where('id', '=', $user_id)
            ->first();

        return response($u, Response::HTTP_OK);
    }

    public function create(Request $request): Response
    {
        if ($request->input('password') !== $request->input('password_repeat')) {
            return response('Passwords do not match.', Response::HTTP_BAD_REQUEST);
        }
        if (!filter_var($request->input('email'), FILTER_VALIDATE_EMAIL)) {
            return response('Email address is not a valid email address.', Response::HTTP_BAD_REQUEST);
        }
        if (User::query()->where('email', '=', $request->input('email'))->count() > 0) {
            return response('A user with that email address already exists.', Response::HTTP_BAD_REQUEST);
        }
        if (!array_key_exists($request->input('timezone'), UserDateHandler::timeZones())) {
            return response('Invalid timezone.', Response::HTTP_BAD_REQUEST);
        }

        $user              =  new User();
        $user->email       = $request->input('email');
        $user->name        = $request->input('name');
        $user->password    = bcrypt($request->input('password'));
        $user->timezone    = $request->input('timezone');
        $user->description = '';
        $user->save();

        return response($user, Response::HTTP_OK);
    }

    public function getGuilds(Request $request)
    {
        /** @var User $user */
        $user = $this->getRequestUser($request);

        $guilds = $user->guilds();

        foreach ($guilds as $guild) {
            $guild->member_count = count($guild->users(Guild::MEMBERSHIP_STATUS_MEMBER));
        }

        return \response($guilds, Response::HTTP_OK);
    }

    public function getGuildsPending(Request $request)
    {
        /** @var User $user */
        $user = $this->getRequestUser($request);

        return \response($user->guildsPending(), Response::HTTP_OK);
    }

    public function getEvents(Request $request)
    {
        /** @var User $user */
        $user = $this->getRequestUser($request);

        return \response($user->upcomingEvents(), Response::HTTP_OK);
    }

    public function getEventsSignedUp(Request $request)
    {
        /** @var User $user */
        $user = $this->getRequestUser($request);

        $return = [];

        foreach ($user->upcomingEvents() as $upcomingEvent) {
            if ($upcomingEvent->issignedUp($user)) {
                $return[] = $upcomingEvent;
            }
        }

        return \response($return, Response::HTTP_OK);
    }

    public function getSignups(Request $request)
    {
        /** @var User $user */
        $user = $this->getRequestUser($request);

        return \response($user->signups, Response::HTTP_OK);
    }

    public function timeZones()
    {
        return response(UserDateHandler::timeZones(), Response::HTTP_OK);
    }

    public function getCharacters(Request $request)
    {
        /** @var User $user */
        $user = $this->getRequestUser($request);

        return response($user->characters ?? [], Response::HTTP_OK);
    }
}
