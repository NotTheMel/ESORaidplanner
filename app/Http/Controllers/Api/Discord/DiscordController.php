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
use App\GuildLogger;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DiscordController extends Controller
{
    protected $logger;

    public function __construct()
    {
        $this->logger = new GuildLogger();
    }

    public function setup(Request $request)
    {
        /** @var User $user */
        $user  = User::query()
            ->whereNotNull('discord_id')
            ->where('discord_id', '=', $request->input('discord_user_id'))
            ->first();

        if (empty($request->input('guild_id'))) {
            if (0 === count($user->getGuildsWhereIsAdmin())) {
                return \response($user->getDiscordMention().', You are currently not an admin in any guild.', Response::HTTP_OK);
            }
            $return = $user->getDiscordMention().', Please type !setup and then the id of the guild you would like to use. Guilds you are an admin of are listed below:'.PHP_EOL.'```'.PHP_EOL;
            foreach ($user->getGuildsWhereIsAdmin() as $guild) {
                $return .= $guild->id.': '.$guild->name.PHP_EOL;
            }
            $return .= '```';

            return response($return, Response::HTTP_OK);
        }
        /** @var Guild $guild */
        $guild = Guild::query()->find($request->input('guild_id'));

        if (!$guild->isAdmin($user)) {
            return response($user->getDiscordMention().', You are not an admin of this guild.', Response::HTTP_UNAUTHORIZED);
        }

        $guild->discord_id         = $request->input('discord_server_id');
        $guild->discord_channel_id = $request->input('discord_channel_id');
        $guild->save();

        $this->logger->addDiscordBot($guild, $user);

        return response($user->getDiscordMention().', The bot is now set up for '.$guild->name.'.', Response::HTTP_OK);
    }

    public function signUp(Request $request)
    {
        /** @var Event $event */
        $event = Event::query()->find($request->input('event_id'));
        $user  = User::query()
            ->whereNotNull('discord_id')
            ->where('discord_id', '=', $request->input('discord_user_id'))
            ->first();

        $event->signup($user, $request->input('role'), $request->input('class'));

        return response($user->getDiscordMention().', You signed up for '.$event->name.'.', Response::HTTP_OK);
    }

    public function signOff(Request $request)
    {
        /** @var Event $event */
        $event = Event::query()->find($request->input('event_id'));
        $user  = User::query()
            ->whereNotNull('discord_id')
            ->where('discord_id', '=', $request->input('discord_user_id'))
            ->first();

        $event->signoff($user);

        return response($user->getDiscordMention().', You signed off for '.$event->name.'.', Response::HTTP_OK);
    }

    public function listEvents(Request $request)
    {
        /** @var Guild $guild */
        $guild = Guild::query()->where('discord_id', '=', $request->input('discord_server_id'))->first();
        $user  = User::query()
            ->whereNotNull('discord_id')
            ->where('discord_id', '=', $request->input('discord_user_id'))
            ->first();

        $return = $user->getDiscordMention().', Upcoming events for '.$guild->name.':'.PHP_EOL;
        $return .= '```';
        if (0 === count($guild->getEvents())) {
            $return .= 'No events available'.PHP_EOL;
        } else {
            foreach ($guild->getEvents() as $event) {
                $return .= $event->id.': '.$event->name.PHP_EOL;
            }
        }
        $return .= '```';

        return response($return, Response::HTTP_OK);
    }

    public function getLastActivity(Request $request)
    {
        $guilds = Guild::query()
            ->select(['id', 'discord_id', 'discord_last_activity'])
            ->whereNotNull('discord_id')
            ->whereNotNull('discord_last_activity')
            ->get()->all();

        $return = [];

        foreach ($guilds as $guild) {
            $dt = new \DateTime($guild->discord_last_activity, new \DateTimeZone(env('DEFAULT_TIMEZONE')));
            $dt->setTimezone(new \DateTimeZone('UTC'));
            $guild->discord_last_activity = $dt->format('Y-m-d H:i:s');
            $return[]                     = $guild;
        }

        return response($return, Response::HTTP_OK);
    }
}
