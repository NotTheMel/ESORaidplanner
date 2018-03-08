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

use App\Hook\GuildApplicationNotification;
use App\Singleton\HookTypes;
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
    public function userStatus(User $user): int
    {
        $guild = DB::table('user_guilds')
            ->where('guild_id', '=', $this->id)
            ->where('user_id', '=', $user->id)
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
     * @param User $user
     *
     * @return bool
     */
    public function isAdmin(User $user): bool
    {
        $admins = json_decode($this->admins, true);

        if (in_array($user->id, $admins, false)) {
            return true;
        }

        return false;
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
            ->get()->all();

        return $events;
    }

    public function getPastEvents()
    {
        $events = Event::query()
            ->where('guild_id', '=', $this->id)
            ->where('start_date', '<', date('Y-m-d H:i:s'))
            ->orderBy('start_date', 'desc')
            ->get()->all();

        return $events;
    }

    /**
     * @param User      $user
     * @param User|null $admin
     */
    public function makeAdmin(User $user, User $admin = null)
    {
        $admin = $admin ?? Auth::user();
        if (!$this->isOwner($admin)) {
            return;
        }
        $admins = json_decode($this->admins, true);

        if (!in_array($user->id, $admins)) {
            $admins[] = $user->id;
            $log      = new LogEntry();
            $log->create($this->id, $admin->name.' promoted '.$user->name.' to admin.');
        }

        self::query()
            ->where('id', '=', $this->id)
            ->update(['admins' => json_encode($admins)]);
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function isOwner(User $user): bool
    {
        return $this->owner_id === $user->id;
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
     * @param User $user
     *
     * @return bool
     */
    public function isMember(User $user): bool
    {
        $count = DB::table('user_guilds')->where('guild_id', '=', $this->id)
            ->where('user_id', '=', $user->id)
            ->where('status', '>=', 1)
            ->count();

        if (1 === $count) {
            return true;
        }

        return false;
    }

    public function leave(User $user)
    {
        if ($this->isOwner($user)) {
            return;
        }

        $this->removeAdmin($user);

        DB::table('user_guilds')->where('user_id', '=', $user->id)->where('guild_id', '=', $this->id)->delete();

        $log = new LogEntry();
        $log->create($this->id, $user->name.' left the guild.');
    }

    /**
     * @param User      $user
     * @param User|null $admin
     */
    public function removeAdmin(User $user, User $admin = null)
    {
        $admin = $admin ?? Auth::user();
        if (!$this->isOwner($admin)) {
            return;
        }
        $admins = json_decode($this->admins, true);

        if (in_array($user->id, $admins)) {
            $arr = array_diff($admins, [$user->id]);

            self::query()
                ->where('id', '=', $this->id)
                ->update(['admins' => json_encode($arr)]);

            $log = new LogEntry();
            $log->create($this->id, $admin->name.' demoted '.$user->name.' to member.');
        }
    }

    public function getMembers()
    {
        return User::query()
            ->join('user_guilds', 'users.id', 'user_guilds.user_id')
            ->where('user_guilds.guild_id', '=', $this->id)
            ->where('user_guilds.status', '>=', 1)
            ->orderBy('users.name', 'asc')
            ->get(['users.*'])->all();
    }

    public function getPendingMembers()
    {
        return User::query()
            ->join('user_guilds', 'users.id', 'user_guilds.user_id')
            ->where('user_guilds.guild_id', '=', $this->id)
            ->where('user_guilds.status', '=', 0)
            ->orderBy('users.name', 'asc')
            ->get(['users.*'])->all();
    }

    public function requestMembership(User $user)
    {
        $count = DB::table('user_guilds')
            ->where('user_id', '=', $user->id)
            ->where('guild_id', '=', $this->id)
            ->count();

        if ($count > 0) {
            return;
        }

        DB::table('user_guilds')->insert([
            'user_id'  => $user->id,
            'guild_id' => $this->id,
            'status'   => 0,
        ]);

        $log = new LogEntry();
        $log->create($this->id, $user->name.' requested membership.');

        $hooks = GuildApplicationNotification::query()->where('guild_id', '=', $this->id)
            ->where('call_type', '=', HookTypes::ON_GUIDMEMBER_APPLICATION)
            ->get()->all();

        foreach ($hooks as $hook) {
            $hook->call();
        }
    }

    public function approveMembership(User $user, User $admin = null)
    {
        DB::table('user_guilds')->where('user_id', '=', $user->id)->where('guild_id', '=', $this->id)->update(['status' => 1]);

        $admin = $admin ?? Auth::user();

        $log = new LogEntry();
        $log->create($this->id, $admin->name.' approved the membership request of '.$user->name.'.');
    }

    public function removeMembership(User $user, User $admin = null)
    {
        DB::table('user_guilds')->where('user_id', '=', $user->id)->where('guild_id', '=', $this->id)->delete();

        $this->removeAdmin($user);

        $admin = $admin ?? Auth::user();

        $log = new LogEntry();
        $log->create($this->id, $admin->name.' removed '.$user->name.' from the guild.');
    }
}
