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

use App\Event;
use App\Guild;
use App\User;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class GuildController extends ApiController
{
    /**
     * Gets all guilds associated with the user.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function all(Request $request): Response
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        return response(Guild::query()->orderBy('name')->get(), Response::HTTP_OK);
    }

    /**
     * Get a guild, but only if the user is a member.
     *
     * @param Request $request
     * @param int     $guild_id
     *
     * @return Response
     */
    public function get(Request $request, int $guild_id): Response
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        /** @var Guild $guild */
        $guild = Guild::query()->find($guild_id);

        if (!$guild->isMember($user)) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        return response($guild, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int     $guild_id
     *
     * @return Response
     */
    public function requestMembership(Request $request, int $guild_id): Response
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        /** @var Guild $guild */
        $guild = Guild::query()->find($guild_id);

        $guild->requestMembership($user);

        return response(null, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int     $guild_id
     *
     * @return Response
     */
    public function leave(Request $request, int $guild_id): Response
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        /** @var Guild $guild */
        $guild = Guild::query()->find($guild_id);

        $guild->leave($user);

        return response(null, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int     $guild_id
     * @param int     $user_id
     *
     * @return Response
     */
    public function approveMembership(Request $request, int $guild_id, int $user_id): Response
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        /** @var Guild $guild */
        $guild = Guild::query()->find($guild_id);
        /** @var User $user_1 */
        $user_1 = User::query()->find($user_id);

        if (!$guild->isAdmin($user)) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        $guild->approveMembership($user_1, $user);

        return response(null, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int     $guild_id
     * @param int     $user_id
     *
     * @return Response
     */
    public function removeMembership(Request $request, int $guild_id, int $user_id): Response
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        /** @var User $user_1 */
        $user_1 = User::query()->find($user_id);
        /** @var Guild $guild */
        $guild = Guild::query()->find($guild_id);

        if (!$guild->isAdmin($user)) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }
        if (!$guild->isOwner($user) && $guild->isAdmin($user_1)) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        $guild->removeMembership($user_1, $user);

        return response(null, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int     $guild_id
     * @param int     $user_id
     *
     * @return Response
     */
    public function makeAdmin(Request $request, int $guild_id, int $user_id): Response
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        /** @var Guild $guild */
        $guild = Guild::query()->find($guild_id);
        /** @var User $user_1 */
        $user_1 = User::query()->find($user_id);

        if (!$guild->isOwner($user)) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        $guild->makeAdmin($user_1, $user);

        return response(null, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int     $guild_id
     * @param int     $user_id
     *
     * @return Response
     */
    public function removeAdmin(Request $request, int $guild_id, int $user_id): Response
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        /** @var Guild $guild */
        $guild = Guild::query()->find($guild_id);
        /** @var User $user_1 */
        $user_1 = User::query()->find($user_id);

        if (!$guild->isOwner($user)) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        $guild->removeAdmin($user_1, $user);

        return response(null, Response::HTTP_OK);
    }

    /**
     * Get all upcoming events for a guild.
     *
     * NEEDS TO BE REDONE TO NOT GIVE PAST EVENTS
     *
     * @param Request $request
     * @param int     $guild_id
     *
     * @return Response
     */
    public function getEvents(Request $request, int $guild_id): Response
    {
        $user = $this->login($request);

        /** @var Guild $guild */
        $guild = Guild::query()->find($guild_id);

        if (false === $user || !$guild->isMember($user)) {
            return response(null, Response::HTTP_UNAUTHORIZED);
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

        return response($e ?? [], Response::HTTP_OK);
    }

    /**
     * Get all members of a guild (limited user information).
     *
     * @param Request $request
     * @param int     $guild_id
     *
     * @return Response
     */
    public function getMembers(Request $request, int $guild_id): Response
    {
        $user = $this->login($request);

        /** @var Guild $guild */
        $guild = Guild::query()->find($guild_id);

        if (false === $user || !$guild->isMember($user)) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        $members = DB::table('user_guilds')
            ->select('users.id as user_id', 'users.name as name', 'users.avatar as avatar', 'user_guilds.guild_id as guild_id', 'user_guilds.status as status')
            ->join('users', 'user_guilds.user_id', 'users.id')
            ->where('user_guilds.guild_id', '=', $guild->id)
            ->where('user_guilds.status', '>=', 1)
            ->orderBy('users.name', 'asc')
            ->get();

        return response($members ?? [], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int     $guild_id
     *
     * @return Response
     */
    public function getMembersPending(Request $request, int $guild_id): Response
    {
        $user = $this->login($request);

        /** @var Guild $guild */
        $guild = Guild::query()->find($guild_id);

        if (false === $user || !$guild->isAdmin($user)) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        $members = DB::table('user_guilds')
            ->select('users.id as user_id', 'users.name as name', 'users.avatar as avatar', 'user_guilds.guild_id as guild_id', 'user_guilds.status as status')
            ->join('users', 'user_guilds.user_id', 'users.id')
            ->where('user_guilds.guild_id', '=', $guild->id)
            ->where('user_guilds.status', '=', 0)
            ->orderBy('users.name', 'asc')
            ->get();

        return response($members ?? [], Response::HTTP_OK);
    }
}
