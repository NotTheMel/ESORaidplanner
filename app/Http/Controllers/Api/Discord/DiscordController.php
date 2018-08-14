<?php
/**
 * This file is part of the ESO-Database project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 *
 * @see https://eso-database.com
 * Created by woeler
 * Date: 14.08.18
 * Time: 18:56
 */

namespace App\Http\Controllers\Api\Discord;

use App\Event;
use App\Guild;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DiscordController extends Controller
{
    public function signUp(Request $request)
    {
        /** @var Event $event */
        $event = Event::query()->find($request->input('event_id'));
        $user  = User::query()->where('discord_handle', '=', $request->input('user_id'))->first();

        $event->signup($user, $request->input('role'), $request->input('class'));

        return response('', Response::HTTP_OK);
    }

    public function signOff(Request $request)
    {
        /** @var Event $event */
        $event = Event::query()->find($request->input('event_id'));
        $user  = User::query()->where('discord_handle', '=', $request->input('user_id'))->first();

        $event->signoff($user);

        return response('', Response::HTTP_OK);
    }

    public function listEvents(Request $request)
    {
        /** @var Guild $guild */
        $guild = Guild::query()->where('discord_id', '=', $request->input('guild_id'))->first();

        return response($guild->getEvents() ?? [], Response::HTTP_OK);
    }
}
