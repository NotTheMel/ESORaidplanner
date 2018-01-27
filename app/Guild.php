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

use DateTime;
use DateTimeZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Guild extends Model
{
    /**
     * @return string
     */
    public function getMegaserver(): string
    {
        return DataMapper::getMegaserverName($this->megaserver);
    }

    /**
     * @return string
     */
    public function getPlatform(): string
    {
        return DataMapper::getPlatformName($this->platform);
    }

    /**
     * @return int
     */
    public function userStatus(): int
    {
        $guild = DB::table('user_guilds')
            ->where('guild_id', '=', $this->id)
            ->where('user_id', '=', Auth::id())
            ->first();

        if (empty($guild)) {
            return -1;
        }

        return $guild->status;
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public function getMemberName(int $id): string
    {
        $member = User::query()
            ->where('id', '=', $id)
            ->first();

        return $member->name;
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function isAdmin(int $id): bool
    {
        $admins = json_decode($this->admins, true);

        if (in_array($id, $admins, false)) {
            return true;
        }

        return false;
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function isOwner(int $id): bool
    {
        return $this->owner_id === $id;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getEvents()
    {
        $events = Event::query()
            ->where('guild_id', '=', $this->id)
            ->where('start_date', '>=', date('Y-m-d H:i:s'))
            ->orderBy('start_date', 'asc')
            ->get();

        return $events;
    }

    /**
     * @param int $user_id
     */
    public function makeAdmin(int $user_id)
    {
        if (!$this->isOwner(Auth::id())) {
            return;
        }
        $admins = json_decode($this->admins, true);

        $admins[] = $user_id;

        self::query()
            ->where('id', '=', $this->id)
            ->update(['admins' => json_encode($admins)]);

        $user = User::query()->find($user_id);

        $log = new LogEntry();
        $log->create($this->id, Auth::user()->name.' promoted '.$user->name.' to admin.');
    }

    /**
     * @param int $user_id
     */
    public function removeAdmin(int $user_id, int $admin_id = null)
    {
        if (!$this->isOwner($admin_id ?? Auth::id())) {
            return;
        }
        $admins = json_decode($this->admins, true);

        if (in_array($user_id, $admins)) {
            $arr = array_diff($admins, [$user_id]);

            self::query()
                ->where('id', '=', $this->id)
                ->update(['admins' => json_encode($arr)]);

            $user = User::query()->find($user_id);

            $log = new LogEntry();
            $log->create($this->id, Auth::user()->name.' demoted '.$user->name.' to member.');
        }
    }

    /**
     * @return string
     */
    public function getNiceDate(): string
    {
        $date = new DateTime($this->created_at);

        $date->setTimezone(new DateTimeZone(Auth::user()->timezone));

        if (12 === Auth::user()->clock) {
            return $date->format('F jS g:i a');
        }

        return $date->format('F jS H:i');
    }

    /**
     * @param int|null $id
     *
     * @return bool
     */
    public function isMember(int $id = null): bool
    {
        $count = DB::table('user_guilds')->where('guild_id', '=', $this->id)
            ->where('user_id', '=', $id ?? Auth::id())
            ->where('status', '>=', 1)
            ->count();

        if (1 === $count) {
            return true;
        }

        return false;
    }
}
