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

use App\Hook\ConfirmedSignupsNotification;
use App\Hook\GuildApplicationNotification;
use App\Hook\NotificationHook;
use App\Singleton\HookTypes;
use DateTime;
use DateTimeZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Guild extends Model
{
    public $logger;

    protected $fillable = [
        'name',
        'slug',
        'megaserver',
        'platform',
        'admins',
        'owner_id',
        'image',
        'discord_widget',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->logger = new GuildLogger();
    }

    /**
     * @return bool|null|void
     */
    public function delete()
    {
        NotificationHook::query()->where('guild_id', '=', $this->id)->delete();
        $events = Event::query()->where('guild_id', '=', $this->id)->get();

        foreach ($events as $event) {
            Signup::query()->where('event_id', '=', $event->id)->delete();
        }

        Event::query()->where('guild_id', '=', $this->id)->delete();
        DB::table('user_guilds')->where('guild_id', '=', $this->id)->delete();
        LogEntry::query()->where('guild_id', '=', $this->id)->delete();

        parent::delete();
    }

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

    /**
     * @return array
     */
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
        if ($this->owner_id !== $user->id) {
            return;
        }
        $admins = json_decode($this->admins, true);

        if (!in_array($user->id, $admins)) {
            $admins[] = $user->id;
            $this->logger->guildMakeAdmin($this, $admin, $user);
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

    /**
     * @param User $user
     */
    public function leave(User $user)
    {
        if ($this->owner_id === $user->id) {
            return;
        }

        $this->removeAdmin($user);
        $this->removeFromAllTeams($user);

        DB::table('user_guilds')->where('user_id', '=', $user->id)->where('guild_id', '=', $this->id)->delete();

        $this->logger->guildLeave($this, $user);
    }

    /**
     * @param User      $user
     * @param User|null $admin
     */
    public function removeAdmin(User $user, User $admin = null)
    {
        $admin = $admin ?? Auth::user();
        if ($this->owner_id !== $user->id) {
            return;
        }
        $admins = json_decode($this->admins, true);

        if (in_array($user->id, $admins)) {
            $arr = array_diff($admins, [$user->id]);

            self::query()
                ->where('id', '=', $this->id)
                ->update(['admins' => json_encode($arr)]);

            $this->logger->guildRemoveAdmin($this, $admin, $user);
        }
    }

    /**
     * @return array
     */
    public function getMembers()
    {
        return User::query()
            ->join('user_guilds', 'users.id', 'user_guilds.user_id')
            ->where('user_guilds.guild_id', '=', $this->id)
            ->where('user_guilds.status', '>=', 1)
            ->orderBy('users.name', 'asc')
            ->get(['users.*'])->all();
    }

    /**
     * @return array
     */
    public function getMembersIdArray(): array
    {
        $members =[];
        foreach ($this->getMembers() as $member) {
            $members[$member->id] = $member->name;
        }

        return $members;
    }

    /**
     * @return array
     */
    public function getPendingMembers()
    {
        return User::query()
            ->join('user_guilds', 'users.id', 'user_guilds.user_id')
            ->where('user_guilds.guild_id', '=', $this->id)
            ->where('user_guilds.status', '=', 0)
            ->orderBy('users.name', 'asc')
            ->get(['users.*'])->all();
    }

    /**
     * @param User $user
     */
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

        $this->logger->guildRequestMembership($this, $user);

        $hooks = GuildApplicationNotification::query()->where('guild_id', '=', $this->id)
            ->where('call_type', '=', HookTypes::ON_GUIDMEMBER_APPLICATION)
            ->get()->all();

        foreach ($hooks as $hook) {
            $hook->call($user);
        }
    }

    /**
     * @param User      $user
     * @param User|null $admin
     */
    public function approveMembership(User $user, User $admin = null)
    {
        DB::table('user_guilds')->where('user_id', '=', $user->id)->where('guild_id', '=', $this->id)->update(['status' => 1]);

        $admin = $admin ?? Auth::user();

        $this->logger->guildApproveMembership($this, $admin, $user);
    }

    /**
     * @param User      $user
     * @param User|null $admin
     */
    public function removeMembership(User $user, User $admin = null)
    {
        DB::table('user_guilds')->where('user_id', '=', $user->id)->where('guild_id', '=', $this->id)->delete();

        $this->removeAdmin($user);
        $this->removeFromAllTeams($user);

        $admin = $admin ?? Auth::user();

        $this->logger->guildRemoveMembership($this, $admin, $user);
    }

    /**
     * @return bool
     */
    public function hasConfirmedSignupsHooks(): bool
    {
        $hooks = ConfirmedSignupsNotification::query()->where('call_type', '=', HookTypes::CONFIRMED_SIGNUPS)
            ->where('guild_id', '=', $this->id)
            ->count();

        return $hooks > 0;
    }

    /**
     * @return array
     */
    public function getTeams(): array
    {
        return Team::query()
                ->where('guild_id', '=', $this->id)
                ->orderBy('name')
                ->get()->all() ?? [];
    }

    /**
     * @return array
     */
    public function getRepeatableEvents(): array
    {
        return RepeatableEvent::query()->where('guild_id', '=', $this->id)->get()->all() ?? [];
    }

    /**
     * @return bool
     */
    public function hasDiscordBot(): bool
    {
        return !empty($this->discord_id) && !empty($this->discord_channel_id);
    }

    /**
     * @param User $user
     */
    private function removeFromAllTeams(User $user)
    {
        $teams = Team::query()->where('guild_id', '=', $this->id)->get()->all() ?? [];

        foreach ($teams as $team) {
            $team->removeMember($user->id);
        }
    }
}
