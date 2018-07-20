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

use App\Guild;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

        if (!$guild->isMember($user)) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        $events = $guild->getEvents();

        foreach ($events as $event) {
            $event->start_date = $event->getUtcTime();
        }

        return response($events ?? [], Response::HTTP_OK);
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

        if (!$guild->isMember($user)) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        $members = $guild->getMembers();

        /* For deprecated app */
        foreach ($members as $member) {
            $member->user_id = $member->id;
        }

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

        if (!$guild->isAdmin($user)) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        $members = $guild->getPendingMembers();

        /* For deprecated app */
        foreach ($members as $member) {
            $member->user_id = $member->id;
        }

        return response($members ?? [], Response::HTTP_OK);
    }
}
