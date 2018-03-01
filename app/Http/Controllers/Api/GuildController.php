<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 01.03.18
 * Time: 22:56.
 */

namespace App\Http\Controllers\Api;

use App\Event;
use App\Guild;
use App\LogEntry;
use App\User;
use DateTime;
use DateTimeZone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuildController extends ApiController
{
    /**
     * Gets all guilds associated with the user.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function all(Request $request): JsonResponse
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        return response(Guild::query()->orderBy('name')->get(), 200);
    }

    /**
     * Get a guild, but only if the user is a member.
     *
     * @param Request $request
     * @param int     $guild_id
     *
     * @return JsonResponse
     */
    public function get(Request $request, int $guild_id): JsonResponse
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $count = DB::table('user_guilds')->where('user_id', '=', $user->id)->where('guild_id', '=', $guild_id)->count();

        if (0 === $count) {
            return response(null, 401);
        }

        return response(Guild::query()->find($guild_id), 200);
    }

    /**
     * @param Request $request
     * @param int     $guild_id
     *
     * @return JsonResponse
     */
    public function requestMembership(Request $request, int $guild_id): JsonResponse
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

        return response(null, 200);
    }

    /**
     * @param Request $request
     * @param int     $guild_id
     *
     * @return JsonResponse
     */
    public function leave(Request $request, int $guild_id): JsonResponse
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        /** @var Guild $guild */
        $guild = Guild::query()->find($guild_id);

        if ($guild->isOwner($user->id)) {
            return response(null, 401);
        }

        if ($guild->isAdmin($user->id)) {
            $guild->removeAdmin($user->id, $user->id);
        }

        DB::table('user_guilds')->where('user_id', '=', $user->id)->where('guild_id', '=', $guild->id)->delete();

        $log = new LogEntry();
        $log->create($guild->id, $user->name.' left the guild. (Via App)');

        return response(null, 200);
    }

    /**
     * @param Request $request
     * @param int     $guild_id
     * @param int     $user_id
     *
     * @return JsonResponse
     */
    public function approveMembership(Request $request, int $guild_id, int $user_id): JsonResponse
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        /** @var Guild $guild */
        $guild = Guild::query()->find($guild_id);

        if (!$guild->isAdmin($user->id)) {
            return response(null, 401);
        }

        DB::table('user_guilds')->where('user_id', '=', $user_id)->where('guild_id', '=', $guild_id)->update(['status' => 1]);

        $user_1 = User::query()->find($user_id);

        $log = new LogEntry();
        $log->create($guild->id, $user->name.' approved the membership request of '.$user_1->name.'. (Via App)');

        return response(null, 200);
    }

    /**
     * @param Request $request
     * @param int     $guild_id
     * @param int     $user_id
     *
     * @return JsonResponse
     */
    public function removeMembership(Request $request, int $guild_id, int $user_id): JsonResponse
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        /** @var Guild $guild */
        $guild = Guild::query()->find($guild_id);

        if ($guild->owner_id === $user_id || !$guild->isAdmin($user->id)) {
            return response(null, 401);
        }

        DB::table('user_guilds')->where('user_id', '=', $user_id)->where('guild_id', '=', $guild_id)->delete();

        $guild->removeAdmin($user_id, $user->id);

        $user_1 = User::query()->find($user_id);

        $log = new LogEntry();
        $log->create($guild->id, $user->name.' removed '.$user_1->name.' from the guild. (Via App)');

        return response(null, 200);
    }

    /**
     * @param Request $request
     * @param int     $guild_id
     * @param int     $user_id
     *
     * @return JsonResponse
     */
    public function makeAdmin(Request $request, int $guild_id, int $user_id): JsonResponse
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        /** @var Guild $guild */
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

        return response(null, 200);
    }

    /**
     * @param Request $request
     * @param int     $guild_id
     * @param int     $user_id
     *
     * @return JsonResponse
     */
    public function removeAdmin(Request $request, int $guild_id, int $user_id): JsonResponse
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        /** @var Guild $guild */
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

        return response(null, 200);
    }

    /**
     * Get all upcoming events for a guild.
     *
     * @param Request $request
     * @param int     $guild_id
     *
     * @return JsonResponse
     */
    public function getEvents(Request $request, int $guild_id): JsonResponse
    {
        $user = $this->login($request);

        /** @var Guild $guild */
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

        return response($e ?? [], 200);
    }

    /**
     * Get all members of a guild (limited user information).
     *
     * @param Request $request
     * @param int     $guild_id
     *
     * @return JsonResponse
     */
    public function getMembers(Request $request, int $guild_id): JsonResponse
    {
        $user = $this->login($request);

        /** @var Guild $guild */
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

        return response($members ?? [], 200);
    }

    /**
     * @param Request $request
     * @param int     $guild_id
     *
     * @return JsonResponse
     */
    public function getMembersPending(Request $request, int $guild_id): JsonResponse
    {
        $user = $this->login($request);

        /** @var Guild $guild */
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

        return response($members ?? [], 200);
    }
}
