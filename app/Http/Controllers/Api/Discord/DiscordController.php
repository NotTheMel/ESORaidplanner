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

use App\Character;
use App\Event;
use App\Guild;
use App\Http\Controllers\Controller;
use App\Notification\System\AbstractNotificationSystem;
use App\Signup;
use App\User;
use App\Utility\Classes;
use App\Utility\DiscordBotMessages;
use App\Utility\GuildLogger;
use App\Utility\Roles;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Woeler\DiscordPhp\Message\DiscordTextMessage;
use Woeler\DiscordPhp\Webhook\DiscordWebhook;

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
            if (0 === count($user->guildsWhereIsAdmin())) {
                return \response($user->getDiscordMention().', You are currently not an admin in any guild.', Response::HTTP_OK);
            }
            $return = $user->getDiscordMention().', Please type !setup and then the id of the guild you would like to use. Guilds you are an admin of are listed below:'.PHP_EOL.'```'.PHP_EOL;
            foreach ($user->guildsWhereIsAdmin() as $guild) {
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
        $user  = User::query()
            ->whereNotNull('discord_id')
            ->where('discord_id', '=', $request->input('discord_user_id'))
            ->first();

        Log::info('class'.$request->input('class'));
        Log::info('Role'.$request->input('role'));

        if (empty($request->input('event_id'))) {
            return response($user->getDiscordMention().', You did not specify an event id.', Response::HTTP_BAD_REQUEST);
        }
        if (!empty($request->input('preset'))) {
            $character = Character::query()->where('name', 'like', $request->input('preset'))
                ->where('user_id', '=', $user->id)
                ->first();
            if (null === $character) {
                return response($user->getDiscordMention().', I do not know that character preset.', Response::HTTP_BAD_REQUEST);
            }
        } else {
            if (empty($request->input('class')) || empty($request->input('role'))) {
                return response($user->getDiscordMention().', You did not specify a class and/or role.', Response::HTTP_BAD_REQUEST);
            }
            $class = (int)$request->input('class');
            $role  = (int)$request->input('role');
            $sets  = [];

            Log::info('class'.$class);
            Log::info('Role'.$role);
        }

        /** @var Event $event */
        $event = Event::query()->find($request->input('event_id'));

        if (!$event->isSignedUp($user)) {
            if (empty($character)) {
                $event->signup($user, $role, $class, $sets);
            } else {
                $event->signupWithCharacter($user, $character);
            }
        } else {
            if (empty($character)) {
                $event->signup($user, $role, $class, $sets);
            } else {
                $event->signupWithCharacter($user, $character);
            }

            return response($this->buildReply(DiscordBotMessages::EDIT, $user, $event, $class ?? $character->class, $role ?? $character->role));
        }

        return response($this->buildReply(DiscordBotMessages::SIGNUP[array_rand(DiscordBotMessages::SIGNUP)], $user, $event, $class ?? $character->class, $role ?? $character->role), Response::HTTP_OK);
    }

    public function signOff(Request $request)
    {
        $user  = User::query()
            ->whereNotNull('discord_id')
            ->where('discord_id', '=', $request->input('discord_user_id'))
            ->first();

        if (empty($request->input('event_id'))) {
            return response($user->getDiscordMention().', You did not specify an event id.', Response::HTTP_BAD_REQUEST);
        }

        /** @var Event $event */
        $event = Event::query()->find($request->input('event_id'));

        $event->signoff($user);

        return response($this->buildReply(DiscordBotMessages::SIGNOFF, $user, $event), Response::HTTP_OK);
    }

    public function listEvents(Request $request)
    {
        /** @var Guild $guild */
        $guild = Guild::query()->where('discord_id', '=', $request->input('discord_server_id'))->first();
        $user  = User::query()
            ->whereNotNull('discord_id')
            ->where('discord_id', '=', $request->input('discord_user_id'))
            ->first();

        $return = $user->getDiscordMention().', Upcoming events for '.$guild->name.' (date/time based on your configured timezone '.$user->timezone.'):'.PHP_EOL;
        $return .= '```';
        if (0 === count($guild->upcomingEvents())) {
            $return .= 'No events available'.PHP_EOL;
        } else {
            foreach ($guild->upcomingEvents() as $event) {
                $return .= $event->id.': '.$event->name.' ('.$event->getUserHumanReadableDate($user->timezone, $user->clock).')'.PHP_EOL;
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

    public function help()
    {
        return response(DiscordBotMessages::HELP_EMBEDS, Response::HTTP_OK);
    }

    public function status(Request $request)
    {
        /** @var User $user */
        $user  = User::query()
            ->whereNotNull('discord_id')
            ->where('discord_id', '=', $request->input('discord_user_id'))
            ->first();

        if (empty($request->input('event_id'))) {
            return response($user->getDiscordMention().', You did not specify an event id.', Response::HTTP_BAD_REQUEST);
        }

        $event = Event::query()->find($request->input('event_id'));

        if (null === $event) {
            return response($user->getDiscordMention().', I do not know that event.', Response::HTTP_BAD_REQUEST);
        }

        $signup = Signup::query()->where('user_id', '=', $user->id)
            ->where('event_id', '=', $request->input('event_id'))
            ->first();

        if (null !== $signup) {
            return response($this->buildReply(DiscordBotMessages::SIGNED_UP_STATUS, $user, $event, $signup->class_id, $signup->role_id));
        }

        return response($this->buildReply(DiscordBotMessages::NOT_SIGNUP_STATUS, $user, $event));
    }

    public function signups(Request $request)
    {
        /** @var User $user */
        $user  = User::query()
            ->whereNotNull('discord_id')
            ->where('discord_id', '=', $request->input('discord_user_id'))
            ->first();

        if (empty($request->input('event_id'))) {
            return response($user->getDiscordMention().', You did not specify an event id.', Response::HTTP_BAD_REQUEST);
        }

        /** @var Event $event */
        $event = Event::query()->find($request->input('event_id'));

        return response($this->buildEmbedsReplyFromEvent($event), Response::HTTP_OK);
    }

    private function buildReply(string $base, User $user, ?Event $event = null, ?int $class = null, ?int $role = null): string
    {
        $base = str_replace('{USER_MENTION}', $user->getDiscordMention(), $base);
        if (null !== $event) {
            $base = str_replace('{EVENT_NAME}', $event->name, $base);
        }
        if (null !== $class) {
            $base = str_replace('{CLASS}', Classes::CLASSES[$class], $base);
        }
        if (null !== $role) {
            $base = str_replace('{ROLE}', Roles::ROLES[$role], $base);
        }

        return $base;
    }

    private function buildEmbedsReplyFromEvent(Event $event): array
    {
        $data = [
            'username'   => 'ESO Raidplanner',
            'content'    => '',
            'avatar_url' => 'https://esoraidplanner.com'.AbstractNotificationSystem::AVATAR_URL,
            'embeds'     => [[
                'title'       => $event->name,
                'description' => $event->description ?? '',
                'url'         => 'https://esoraidplanner.com/g/'.$event->guild->slug.'/event/'.$event->id,
                'color'       => 9660137,
                'author'      => [
                    'name'     => 'ESO Raidplanner: '.$event->guild->name,
                    'url'      => 'https://esoraidplanner.com',
                    'icon_url' => 'https://esoraidplanner.com/favicon/appicon.jpg',
                ],
                'fields' => [
                    [
                        'name'   => 'Tanks',
                        'value'  => $this->formatSignupsForEmbeds($event->signupsByRole(Roles::TANK)),
                        'inline' => true,
                    ],
                    [
                        'name'   => 'Healers',
                        'value'  => $this->formatSignupsForEmbeds($event->signupsByRole(Roles::HEALER)),
                        'inline' => true,
                    ],
                    [
                        'name'   => 'Magicka DD\'s',
                        'value'  => $this->formatSignupsForEmbeds($event->signupsByRole(Roles::MAGICKA_DD)),
                        'inline' => true,
                    ],
                    [
                        'name'   => 'Stamina DD\'s',
                        'value'  => $this->formatSignupsForEmbeds($event->signupsByRole(Roles::STAMINA_DD)),
                        'inline' => true,
                    ],
                    [
                        'name'   => 'Legend',
                        'value'  => '✅ = Confirmed, ⚠️ = Backup, ❔ = No status',
                        'inline' => false,
                    ],
                ],
                'footer' => [
                    'text'     => 'ESO Raidplanner by Woeler',
                    'icon_url' => 'https://esoraidplanner.com/favicon/appicon.jpg',
                ],
            ]],
        ];

        return $data;
    }

    private function formatSignupsForEmbeds(array $signups): string
    {
        $return = '';
        foreach ($signups as $sign) {
            $u = User::query()->find($sign->user_id);
            if (Signup::STATUS_CONFIRMED === $sign->status) {
                $return .= '✅ ';
            } elseif (Signup::STATUS_BACKUP === $sign->status) {
                $return .= '⚠️ ';
            } else {
                $return .= '❔ ';
            }
            $return .= $u->name.PHP_EOL;
        }
        $return = rtrim($return, PHP_EOL);
        if ('' === $return) {
            $return = 'None';
        }

        return $return;
    }
}
