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

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'timezone', 'clock', 'layout', 'telegram_username', 'discord_handle',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @return array
     */
    public function getGuilds(): array
    {
        $guild_ids = DB::table('user_guilds')
            ->where('user_id', '=', $this->id)
            ->where('status', '>=', 1)
            ->orderBy('guild_id', 'asc')
            ->get();

        $guilds = [];

        foreach ($guild_ids as $guild_id) {
            $guild = Guild::query()
                ->where('id', '=', $guild_id->guild_id)
                ->first();

            $guilds[] = $guild;
        }

        return $guilds;
    }

    /**
     * @return array
     */
    public function getGuildsWhereIsAdmin(): array
    {
        $guilds = $this->getGuilds();
        $return = [];
        foreach ($guilds as $guild) {
            if ($guild->isAdmin($this)) {
                $return[] = $guild;
            }
        }

        return $return;
    }

    /**
     * @return array
     */
    public function getEvents(): array
    {
        $guilds = $this->getGuilds();

        $events = [];

        foreach ($guilds as $guild) {
            foreach ($guild->getEvents() as $event) {
                $events[] = $event;
            }
        }

        usort($events, function ($a, $b) {
            return $a->start_date > $b->start_date;
        });

        return $events;
    }

    /**
     * @return array
     */
    public function getEventsSignedUp(): array
    {
        $events = $this->getEvents();

        $e = [];

        foreach ($events as $event) {
            if ($event->userIsSignedUp($this->id)) {
                $e[] = $event;
            }
        }

        return $e;
    }

    /**
     * @param bool $forsignup
     *
     * @return array|\Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getCharacters(bool $forsignup = false)
    {
        $characters = Character::query()
            ->where('user_id', '=', $this->id)
            ->get();

        if ($forsignup) {
            $arr = [];

            foreach ($characters as $character) {
                $arr[$character->id] = $character->name;
            }

            return $arr;
        }

        return $characters;
    }

    /**
     * @param int $guild_id
     */
    public function sendGuildApproveEmail(int $guild_id)
    {
        $guild = Guild::query()->find($guild_id);

        $user = $this;

        Mail::send('emails.membership_approved', ['user' => $user, 'guild' => $guild], function ($m) use ($user) {
            $m->from('noreply@esoraidplanner.com', '[ESORaidplanner] Guild membership approved');

            $m->to('jurian.janssen@gmail.com', $user->name)->subject('Your Reminder!');
        });
    }

    /**
     * @return string
     */
    public function getMembershipLevel(): string
    {
        switch ($this->membership_level) {
            case 1:
                $return = 'Bronze';
                break;
            case 2:
                $return = 'Silver';
                break;
            case 3:
                $return = 'Gold';
                break;
            case 4:
                $return = 'Platinum';
                break;
            default:
                $return = 'No';
        }

        return $return;
    }

    /**
     * @return mixed
     */
    public function getRace()
    {
        return DB::table('races')->find($this->race);
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return DB::table('classes')->find($this->class);
    }

    /**
     * @return mixed
     */
    public function getAlliance()
    {
        return DB::table('alliances')->find($this->alliance);
    }

    /**
     * @return array
     */
    public function getBadges(): array
    {
        $b = DB::table('user_badges')->where('user_id', '=', $this->id)->orderBy('created_at')->get();

        $badges = [];

        foreach ($b as $badge) {
            $badges[] = Badge::query()->find($badge->badge_id);
        }

        return $badges;
    }

    /**
     * @param $device_id
     * @param string $onesignal_id
     */
    public function addOnesignalId($device_id, string $onesignal_id)
    {
        $ids = json_decode($this->onsesignal_id, true) ?? [];

        $ids[$device_id] = $onesignal_id;

        $this->onesignal_id = json_encode($ids);

        $this->save();
    }

    /**
     * @param $device_id
     */
    public function removeOnesignalId($device_id)
    {
        $ids = json_decode($this->onsesignal_id, true);

        $this->onesignal_id = json_encode(array_except($ids, $device_id));

        $this->save();
    }

    /**
     * @return array
     */
    public function getSignups(): array
    {
        $events = $this->getEvents();

        if (0 === count($events)) {
            return [];
        }

        $signups = [];

        foreach ($events as $event) {
            $s = Signup::query()
                ->where('user_id', '=', $this->id)
                ->where('event_id', '=', $event->id)
                ->first();
            if (!empty($s)) {
                $signups[] = $s;
            }
        }

        return $signups;
    }

    public function getDiscordMention(): string
    {
        return '<@'.$this->discord_id.'>';
    }
}
