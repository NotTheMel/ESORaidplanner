<?php

namespace App\Telegram\Api;

use App\Character;
use App\Comment;
use App\Event;
use App\Guild;
use App\Signup;
use App\User;
use Illuminate\Support\Facades\DB;

class EsoRaidPlannerAPI
{
    /**
     * @param string $username
     *
     * @return array|null
     */
    public static function getGuilds(string $username)
    {
        $user = User::query()->where('telegram_username', '=', $username)->first();

        $guilds = $user->guilds();

        if (!empty($guilds)) {
            return $guilds;
        }

        return null;
    }

    /**
     * @param string $username
     * @param int    $guildId
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public static function getGuild(string $username, int $guildId)
    {
        $guild = Guild::query()->find($guildId);

        if (empty($guild)) {
            return null;
        }

        return $guild;
    }

    /**
     * @param string $username
     * @param int    $guildId
     *
     * @return bool
     */
    public static function isMemberOfGuild(string $username, int $guildId): bool
    {
        $user = User::query()->where('telegram_username', '=', $username)->first();

        $result = DB::table('user_guilds')->where('user_id', '=', $user->id)->where('guild_id', '=', $guildId)->count();

        if ($result > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param string $username
     * @param int    $guildId
     *
     * @return \Illuminate\Database\Eloquent\Collection|null|static[]
     */
    public static function getEvents(string $username, int $guildId)
    {
        $events = Event::query()->where('guild_id', '=', $guildId)->where('start_date', '>', date('Y-m-d H:i:s'))->get();

        if (count($events) > 0) {
            $user = User::query()->where('telegram_username', '=', $username)->first();

            foreach ($events as $event) {
                $event->timeZone = $user->timezone;
            }

            return $events;
        }

        return null;
    }

    /**
     * @param string $username
     * @param int    $guildId
     * @param int    $eventId
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public static function getEvent(string $username, int $guildId, int $eventId)
    {
        $event = Event::query()->find($eventId);

        if (!empty($event)) {
            $user = User::query()->where('telegram_username', '=', $username)->first();

            $event->timeZone = $user->timezone;

            return $event;
        }

        return null;
    }

    /**
     * @param string $username
     * @param int    $guildId
     * @param int    $eventId
     *
     * @return \Illuminate\Database\Eloquent\Collection|null|static[]
     */
    public static function getEventSignups(string $username, int $guildId, int $eventId)
    {
        $signups = Signup::query()->where('event_id', '=', $eventId)->orderBy('created_at', 'asc')->get();

        if (count($signups) > 0) {
            return $signups;
        }

        return null;
    }

    /**
     * @param string $username
     * @param int    $guildId
     *
     * @return array|null
     */
    public static function getSignedEvents(string $username, int $guildId)
    {
        $guild = Guild::query()->find($guildId);

        $user = User::query()->where('telegram_username', '=', $username)->first();

        $events = DB::table('events')
            ->where('start_date', '>', date('Y-m-d H:i:s'))
            ->where('guild_id', '=', $guild->id)
            ->get();

        $un_events = [];

        if (count($events) > 0) {
            foreach ($events as $event) {
                $signup = Signup::query()->where('event_id', '=', $event->id)->where('user_id', '=', $user->id)->first();

                if (!empty($signup)) {
                    $event->timeZone = $user->timezone;
                    array_push($un_events, $event);
                }
            }

            if (count($un_events) > 0) {
                return $un_events;
            }
        }

        return null;
    }

    /**
     * @param string $username
     * @param int    $guildId
     *
     * @return array|null
     */
    public static function getUnsignedEvents(string $username, int $guildId)
    {
        $guild = Guild::query()->find($guildId);

        $user = User::query()->where('telegram_username', '=', $username)->first();

        $events = DB::table('events')
            ->where('start_date', '>', date('Y-m-d H:i:s'))
            ->where('guild_id', '=', $guild->id)
            ->get();

        $un_events = [];

        if (count($events) > 0) {
            foreach ($events as $event) {
                $signup = Signup::query()->where('event_id', '=', $event->id)->where('user_id', '=', $user->id)->first();

                if (empty($signup)) {
                    $event->timeZone = $user->timezone;
                    array_push($un_events, $event);
                }
            }

            if (count($un_events) > 0) {
                return $un_events;
            }
        }

        return null;
    }

    /**
     * @param string $username
     * @param int    $guildId
     * @param int    $eventId
     * @param int    $role
     * @param int    $class
     * @param string $supportSets
     * @param string $comments
     */
    public static function Signup(string $username, int $guildId, int $eventId, int $role, int $class, string $supportSets = null, string $comments = null)
    {
        /** @var Event $event */
        $event = Event::query()->find($eventId);

        $user = User::query()->where('telegram_username', '=', $username)->first();

        if (false === strpos($supportSets, ',')) {
            $sets = [$supportSets] ?? [];
        } else {
            $sets = explode(', ', $supportSets);
        }

        $event->signup($user, $role, $class, $sets);

        if (!empty($comments)) {
            Comment::query()->insert([
                'event_id'   => $event->id,
                'user_id'    => $user->id,
                'text'       => $comments,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * @param string $username
     * @param int    $guildId
     * @param int    $eventId
     */
    public static function Signoff(string $username, int $guildId, int $eventId)
    {
        $user = User::query()->where('telegram_username', '=', $username)->first();
        /** @var Event $event */
        $event = Event::query()->find($eventId);
        $event->signoff($user);
    }

    /**
     * @param string $username
     *
     * @return array|\Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getPresets(string $username)
    {
        $user = User::query()->where('telegram_username', '=', $username)->first();

        $characters = Character::query()->where('user_id', '=', $user->id)->get();

        if (count($characters) > 0) {
            return $characters;
        }

        return [];
    }

    /**
     * @param string $username
     * @param string $name
     * @param int    $role
     * @param int    $class
     * @param string $supportSets
     */
    public static function addPreset(string $username, string $name, int $role, int $class, string $supportSets)
    {
        $user = User::query()->where('telegram_username', '=', $username)->first();

        $arr          = [];
        $arr['name']  = $name;
        $arr['role']  = $role;
        $arr['class'] = $class;
        if (!empty($supportSets)) {
            $arr['sets'] = explode(',', $supportSets);
        } else {
            $arr['sets'] = [];
        }

        $arr['user_id'] = $user->id;

        Character::query()->insert($arr);
    }

    /**
     * @param string $username
     *
     * @return mixed|null
     */
    public static function getProfile(string $username)
    {
        $user = User::query()->where('telegram_username', '=', $username)->first();

        if (!empty($user)) {
            return $user;
        }

        return null;
    }

    /**
     * @param int $id
     *
     * @return mixed|null
     */
    public static function getProfileById(int $id)
    {
        $user = User::query()->where('id', '=', $id)->first();

        if (!empty($user)) {
            return $user;
        }

        return null;
    }
}
