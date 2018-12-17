<?php

namespace App;

use App\Notification\Configuration;
use App\Notification\Message\GuildApplicationMessage;
use App\Notification\Notification;
use App\Utility\GuildLogger;
use App\Utility\MegaServers;
use App\Utility\Platforms;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

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
 * Date: 12.09.18
 * Time: 08:44
 *
 * @property \Illuminate\Database\Eloquent\Collection|\App\Event[]           $events
 * @property \Illuminate\Database\Eloquent\Collection|\App\LogEntry[]        $logs
 * @property \Illuminate\Database\Eloquent\Collection|\App\RepeatableEvent[] $repeatableEvents
 * @property \Illuminate\Database\Eloquent\Collection|\App\Team[]            $teams
 * @mixin \Eloquent
 *
 * @property int                             $id
 * @property string                          $name
 * @property string                          $slug
 * @property int                             $megaserver
 * @property int                             $platform
 * @property mixed                           $admins
 * @property int                             $owner_id
 * @property string                          $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null                     $discord_widget
 * @property string|null                     $discord_id
 * @property string|null                     $discord_channel_id
 * @property string|null                     $discord_last_activity
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guild whereAdmins($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guild whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guild whereDiscordChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guild whereDiscordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guild whereDiscordLastActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guild whereDiscordWidget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guild whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guild whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guild whereMegaserver($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guild whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guild whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guild wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guild whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guild whereUpdatedAt($value)
 */
class Guild extends Model
{
    public const X_REF_USERS               = 'user_guilds';
    public const MEMBERSHIP_STATUS_PENDING = 0;
    public const MEMBERSHIP_STATUS_MEMBER  = 1;

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

    protected $casts = [
        'admins' => 'array',
    ];

    protected $logger;

    public function __construct(array $attributes = [])
    {
        $this->logger = new GuildLogger();
        parent::__construct($attributes);
    }

    /**
     * @param array $options
     *
     * @return bool
     */
    public function save(array $options = []): bool
    {
        if (null !== $this->discord_widget) {
            $this->discord_widget = preg_replace(
                ['/width="\d+"/i'],
                [sprintf('width="%d"', '100')],
                $this->discord_widget);
            $this->discord_widget = str_replace('"100"', '"100%"', $this->discord_widget);
        }

        return parent::save($options);
    }

    public function delete()
    {
        $this->events()->delete();
        $this->teams()->delete();
        $this->logs()->delete();
        $this->repeatableEvents()->delete();
        $this->notifications()->delete();
        DB::table(self::X_REF_USERS)
            ->where('guild_id', '=', $this->id)
            ->delete();

        return parent::delete();
    }

    /**
     * Get all events for the guild.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function events(): HasMany
    {
        return $this->hasMany('App\Event', 'guild_id', 'id');
    }

    /**
     * Get all upcoming events for the guild.
     *
     * @return array
     */
    public function upcomingEvents(): array
    {
        return $this->events()
            ->where('start_date', '>', date('Y-m-d H:i:s'))
            ->orderBy('start_date')
            ->get()->all();
    }

    /**
     * Get all past events for the guild.
     *
     * @return array
     */
    public function pastEvents(): array
    {
        return $this->events()->where('start_date', '<', date('Y-m-d H:i:s'))->get()->all();
    }

    /**
     * @return HasMany
     */
    public function repeatableEvents(): HasMany
    {
        return $this->hasMany('App\RepeatableEvent');
    }

    /**
     * Get all guild teams.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teams(): HasMany
    {
        return $this->hasMany('App\Team');
    }

    /**
     * Get all guild logs.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs(): HasMany
    {
        return $this->hasMany('App\LogEntry');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany('App\Notification\Notification');
    }

    /**
     * Get all users in the guild
     * Limiting to status is optional.
     *
     * @param int|null $status
     *
     * @return array
     */
    public function users(int $status = null): array
    {
        $query = User::query()
            ->select('users.*')
            ->join(self::X_REF_USERS, 'users.id', self::X_REF_USERS.'.user_id')
            ->where(self::X_REF_USERS.'.guild_id', '=', $this->id);
        if (null !== $status) {
            $query->where(self::X_REF_USERS.'.status', '=', $status);
        }

        return $query->orderBy('users.name')->get()->all();
    }

    public function members()
    {
        return User::query()
            ->select('users.*')
            ->join(self::X_REF_USERS, 'users.id', self::X_REF_USERS.'.user_id')
            ->where(self::X_REF_USERS.'.guild_id', '=', $this->id)
                ->orderBy('users.name')
            ->pluck('users.name', 'users.id') ?? [];
    }

    /**
     * Get the guild megaserver.
     *
     * @return string
     */
    public function megaserver(): string
    {
        return MegaServers::MEGASERVERS[$this->megaserver];
    }

    /**
     * Get the guild platform.
     *
     * @return string
     */
    public function platform(): string
    {
        return Platforms::PLATFORMS[$this->platform];
    }

    /**
     * Check if user has administrator permissions in this guild.
     *
     * @param User $user
     *
     * @return bool
     */
    public function isAdmin(User $user): bool
    {
        return \in_array($user->id, $this->admins, true);
    }

    /**
     * Check if user is the guild owner.
     *
     * @param User $user
     *
     * @return bool
     */
    public function isOwner(User $user): bool
    {
        return $this->owner_id === $user->id;
    }

    /**
     * Returns array of admin user id's.
     *
     * @return array
     */
    public function getAdmins(): array
    {
        return $this->admins ?? [];
    }

    /**
     * Check if the user is a member of this guild.
     *
     * @param User $user
     *
     * @return bool
     */
    public function isMember(User $user): bool
    {
        return 1 === DB::table(self::X_REF_USERS)
                ->where('user_id', '=', $user->id)
                ->where('guild_id', '=', $this->id)
                ->where('status', '>', self::MEMBERSHIP_STATUS_PENDING)
                ->count();
    }

    /**
     * Check if the user has pending membership.
     *
     * @param User $user
     *
     * @return bool
     */
    public function isPendingMember(User $user): bool
    {
        return 1 === DB::table(self::X_REF_USERS)
                ->where('user_id', '=', $user->id)
                ->where('guild_id', '=', $this->id)
                ->where('status', '=', self::MEMBERSHIP_STATUS_PENDING)
                ->count();
    }

    /**
     * Apply user for guild membership.
     *
     * @param User $user
     */
    public function applyMember(User $user)
    {
        DB::table(self::X_REF_USERS)
            ->insert([
                'user_id'    => $user->id,
                'guild_id'   => $this->id,
                'status'     => self::MEMBERSHIP_STATUS_PENDING,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

        $this->sendMemberAppliedNotifications($user);
        $this->logger->guildRequestMembership($this, $user);
    }

    /**
     * Approve user's membership request.
     *
     * @param User $user
     */
    public function approveMember(User $user)
    {
        DB::table(self::X_REF_USERS)
            ->where('user_id', '=', $user->id)
            ->where('guild_id', '=', $this->id)
            ->update([
                'status'     => self::MEMBERSHIP_STATUS_MEMBER,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        $this->logger->guildApproveMembership($this, $user);
    }

    /**
     * Remove user as member.
     *
     * @param User $user
     */
    public function removeMember(User $user)
    {
        $this->removeAdmin($user);
        DB::table(self::X_REF_USERS)
            ->where('user_id', '=', $user->id)
            ->where('guild_id', '=', $this->id)
            ->delete();

        $this->logger->guildRemoveMembership($this, $user);
    }

    /**
     * Add user as guild administrator.
     *
     * @param User $user
     */
    public function addAdmin(User $user)
    {
        $admins       = $this->getAdmins();
        $admins[]     = $user->id;
        $this->admins = array_unique($admins);
        $this->save();
        $this->logger->guildMakeAdmin($this, $user);
    }

    /**
     * Remove user as guild administrator.
     *
     * @param User $user
     */
    public function removeAdmin(User $user)
    {
        $admins       = $this->getAdmins();
        $this->admins = array_diff($admins, [$user->id]);
        $this->save();
        $this->logger->guildRemoveAdmin($this, $user);
    }

    /**
     * Transfer the guild ownership to another person.
     *
     * @param User $user
     */
    public function transferOwnership(User $user)
    {
        $this->addAdmin($user);
        $this->owner_id = $user->id;
        $this->save();
        $this->logger->transferOwnership($this, $user);
    }

    /**
     * Checks if the Discord bot is active for this guild.
     *
     * @return bool
     */
    public function hasDiscordBot(): bool
    {
        return !empty($this->discord_id) && !empty($this->discord_channel_id);
    }

    /**
     * Create an event for this guild.
     *
     * @param string      $name
     * @param \DateTime   $start_date
     * @param string|null $description
     * @param array       $tags
     *
     * @return Event
     */
    public function createEvent(string $name, \DateTime $start_date, string $description = null, array $tags = []): Event
    {
        $event = new Event([
            'name'        => $name,
            'start_date'  => $start_date->format('Y-m-d H:i:s'),
            'description' => $description,
            'tags'        => $tags,
            'guild_id'    => $this->id,
        ]);
        $event->save();
        $event->sendCreationNotifications();
        $this->logger->eventCreate($event);

        return $event;
    }

    public function updateEvent(Event $event, string $name, \DateTime $start_date, string $description = null, array $tags = []): Event
    {
        $event->update([
            'name'        => $name,
            'start_date'  => $start_date->format('Y-m-d H:i:s'),
            'description' => $description,
            'tags'        => $tags,
        ]);
        $event->save();

        return $event;
    }

    public function deleteEvent(Event $event)
    {
        $event->delete();
        $this->logger->eventDelete($event);
    }

    /**
     * Create a team for this guild.
     *
     * @param string $name
     *
     * @return Team
     */
    public function createTeam(string $name): Team
    {
        $team = new Team([
            'name'     => $name,
            'guild_id' => $this->id,
        ]);
        $team->save();

        return $team;
    }

    public function dailyNotifications(): array
    {
        return $this->notifications()
            ->whereIn('call_type', Configuration::DAILY_MESSAGES)
            ->get()->all();
    }

    public function createIcalUid(User $user): string
    {
        return base64_encode($user->id.'|'.$this->id.'|'.$this->created_at);
    }

    private function sendMemberAppliedNotifications(User $applicant)
    {
        $notifications = Notification::query()
            ->where('call_type', '=', GuildApplicationMessage::CALL_TYPE)
            ->where('guild_id', '=', $this->guild_id)
            ->get()->all();

        /** @var Notification $notification */
        foreach ($notifications as $notification) {
            $notification->send(['guild' => $this, 'user' => $applicant]);
        }
    }
}
