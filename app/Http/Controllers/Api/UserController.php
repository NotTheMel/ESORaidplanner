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
use App\Signup;
use App\Singleton\TimeZones;
use App\User;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class UserController extends ApiController
{
    /**
     * Get the current user.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function get(Request $request): Response
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        return response($user, Response::HTTP_OK);
    }

    /**
     * Get limited information about a user.
     *
     * @param Request $request
     * @param int     $user_id
     *
     * @return Response
     */
    public function getInfo(Request $request, int $user_id): Response
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        $u = DB::table('users')
            ->select('id', 'name', 'avatar')
            ->where('id', '=', $user_id)
            ->first();

        if (empty($u)) {
            return response(null, Response::HTTP_NOT_FOUND);
        }

        $u1         = new User();
        $u1->avatar = $u->avatar;
        $u1->name   = $u->name;
        $u1->id     = $u->id;

        return response($u1, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request): Response
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
        if (!array_key_exists($request->input('timezone'), TimeZones::list())) {
            return response('Invalid timezone.', 400);
        }

        $user              =  new User();
        $user->email       = $request->input('email');
        $user->name        = $request->input('name');
        $user->password    = bcrypt($request->input('password'));
        $user->timezone    = $request->input('timezone');
        $user->description = '';

        $user->save();

        return response(User::query()->where('email', '=', $request->input('email'))->first(), Response::HTTP_OK);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function setOnesignal(Request $request): Response
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        $device_id    = $request->input('device');
        $onesignal_id = $request->input('onesignal_id');

        if (!empty($device_id) && !empty($onesignal_id)) {
            $user->addOnesignalId($device_id, $onesignal_id);

            return response(null, Response::HTTP_OK);
        }

        return response(null, 400);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function deleteOnesignal(Request $request): Response
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        $device_id = $request->input('device');

        if (!empty($device_id)) {
            $user->removeOnesignalId($device_id);

            return response(null, Response::HTTP_OK);
        }

        return response(null, 400);
    }

    /**
     * Get all guilds of the user (self).
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getGuilds(Request $request): Response
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
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

            $guilds[] = $guild;
        }

        return response($guilds, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function getGuildsPending(Request $request): Response
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        $ids = DB::table('user_guilds')
            ->where('user_id', '=', $user->id)
            ->where('status', '=', 0)
            ->get();

        $guilds = [];

        foreach ($ids as $id) {
            $guilds[] = Guild::query()->find($id->guild_id);
        }

        return response($guilds, Response::HTTP_OK);
    }

    /**
     * Get all events for the user (self).
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getEvents(Request $request): Response
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        $events = [];

        foreach ($user->getGuilds() as $guild) {
            $e = Event::query()->where('guild_id', '=', $guild->id)->get();

            foreach ($e as $a) {
                $date = new DateTime($a->start_date);

                $date->setTimezone(new DateTimeZone('UTC'));

                $a->start_date = $date->format('Y-m-d H:i:s');

                $events[] = $a;
            }
        }

        return response($events, Response::HTTP_OK);
    }

    /**
     * Get all events the user is signed up for (self).
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getEventsSignedUp(Request $request): Response
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        /** @var array $events */
        $events = $user->getEventsSignedUp();

        return response($events, Response::HTTP_OK);
    }

    /**
     * Get all signups for the user (self).
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getSignups(Request $request): Response
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        $events = $user->getEventsSignedUp();

        if (0 === count($events)) {
            return response([], Response::HTTP_OK);
        }

        $signups = [];

        foreach ($events as $event) {
            $signups[] = Signup::query()
                ->where('user_id', '=', $user->id)
                ->where('event_id', '=', $event->id)
                ->first();
        }

        return response($signups, Response::HTTP_OK);
    }
}
