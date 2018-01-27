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
 * @see https://github.com/Woeler/eso-raid-planner
 */

namespace App;

use DateTime;
use DateTimeZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class Event.
 */
class Event extends Model
{
    /**
     * @return int
     */
    public function getTotalSignups(): int
    {
        return DB::table('signups')
            ->where('event_id', $this->id)
            ->count();
    }

    /**
     * @return string
     */
    public function getNiceDate(): string
    {
        $date = new DateTime($this->start_date);

        $date->setTimezone(new DateTimeZone(Auth::user()->timezone));

        if (12 === Auth::user()->clock) {
            return $date->format('F jS g:i a');
        }

        return $date->format('F jS H:i');
    }

    /**
     * @return string
     */
    public function getTypeName(): string
    {
        if (1 === $this->type) {
            return 'Trials';
        } elseif (2 === $this->type) {
            return 'Dungeons';
        } elseif (3 === $this->type) {
            return 'PvP';
        } elseif (4 === $this->type) {
            return 'Guild Meeting';
        } elseif (999 === $this->type) {
            return 'Other';
        }

        return 'Unknown';
    }

    /**
     * @return string
     */
    public function getTypeImage()
    {
        if (1 === $this->type) {
            return asset('img/Header_Event_Trials.jpg');
        } elseif (2 === $this->type) {
            return 'Dungeons';
        } elseif (3 === $this->type) {
            return 'PvP';
        } elseif (4 === $this->type) {
            return 'Guild Meeting';
        } elseif (999 === $this->type) {
            return 'Other';
        }

        return 'Unknown';
    }

    /**
     * @return bool
     */
    public function userIsSignedUp(int $user_id = null): bool
    {
        $result = Signup::query()
            ->where('event_id', $this->id)
            ->where('user_id', '=', $user_id ?? Auth::id())
            ->count();

        if (1 === $result) {
            return true;
        }

        return false;
    }

    /**
     * @param string $type
     *
     * @return mixed
     */
    public function getUserSignup(string $type)
    {
        $result = Signup::query()
            ->where('event_id', $this->id)
            ->where('user_id', '=', Auth::id())
            ->first();

        return $result->$type;
    }

    /**
     * @return Guild
     */
    public function getGuild(): Guild
    {
        return Guild::query()
            ->where('id', '=', $this->guild_id)
            ->first();
    }

    /**
     * @return array
     */
    public function getSignups(): array
    {
        return DB::table('signups')
            ->where('event_id', $this->id);
    }

    /**
     * @return array
     */
    public function getSignUpIds(): array
    {
        $signups = Signup::query()->where('event_id', '=', $this->id)->get();

        $arr = [];

        foreach ($signups as $signup) {
            $arr[] = $signup->user_id;
        }

        return $arr;
    }
}
