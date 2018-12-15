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
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GuildController extends UserApiController
{
    public function get(Request $request, int $guild_id)
    {
        $guild = Guild::query()->find($guild_id);

        return response($guild, Response::HTTP_OK);
    }

    public function all(Request $request)
    {
        $guilds = Guild::query()->where('active', '=', 1)->get()->all();

        return \response($guilds, Response::HTTP_OK);
    }

    public function requestMembership(Request $request, int $guild_id): Response
    {
        $user = $this->getRequestUser($request);
        /** @var Guild $guild */
        $guild = Guild::query()->find($guild_id);
        $guild->applyMember($user);

        return response(null, Response::HTTP_OK);
    }

    public function approveMembership(Request $request, int $guild_id, int $user_id): Response
    {
        /** @var Guild $guild */
        $guild = Guild::query()->find($guild_id);
        /** @var User $user_1 */
        $user_1 = User::query()->find($user_id);

        $guild->approveMember($user_1);

        return response(null, Response::HTTP_OK);
    }

    public function removeMembership(Request $request, int $guild_id, int $user_id)
    {
        /** @var Guild $guild */
        $guild = Guild::query()->find($guild_id);
        /** @var User $user_1 */
        $user_1 = User::query()->find($user_id);

        $guild->removeMember($user_1);

        return response(null, Response::HTTP_OK);
    }

    public function promoteMember(Request $request, int $guild_id, int $user_id)
    {
        /** @var Guild $guild */
        $guild = Guild::query()->find($guild_id);
        /** @var User $user_1 */
        $user_1 = User::query()->find($user_id);

        $guild->addAdmin($user_1);
    }

    public function demoteMember(Request $request, int $guild_id, int $user_id)
    {
        /** @var Guild $guild */
        $guild = Guild::query()->find($guild_id);
        /** @var User $user_1 */
        $user_1 = User::query()->find($user_id);

        $guild->removeAdmin($user_1);
    }

    public function leave(Request $request, int $guild_id): Response
    {
        $user = $this->getRequestUser($request);
        /** @var Guild $guild */
        $guild = Guild::query()->find($guild_id);
        $guild->removeMember($user);

        return response(null, Response::HTTP_OK);
    }

    public function getEvents(Request $request, int $guild_id): Response
    {
        /** @var Guild $guild */
        $guild = Guild::query()->find($guild_id);

        return response($guild->events ?? [], Response::HTTP_OK);
    }

    public function getMembers(Request $request, int $guild_id): Response
    {
        /** @var Guild $guild */
        $guild   = Guild::query()->find($guild_id);
        $members = $guild->users(Guild::MEMBERSHIP_STATUS_MEMBER);

        return response($members ?? [], Response::HTTP_OK);
    }

    public function getPendingMembers(Request $request, int $guild_id): Response
    {
        /** @var Guild $guild */
        $guild   = Guild::query()->find($guild_id);
        $members = $guild->users(Guild::MEMBERSHIP_STATUS_PENDING);

        return response($members ?? [], Response::HTTP_OK);
    }
}
