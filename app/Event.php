<?php

namespace App;

use App\Notification\Message\EventCreationMessage;
use App\Notification\Message\EventDeletionMessage;
use App\Notification\Message\PostSignupsMessage;
use App\Notification\Message\ReminderMessage;
use App\Notification\Notification;
use App\Utility\GuildLogger;
use App\Utility\UserDateHandler;

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
 * Time: 08:42
 *
 * @property \App\Guild                                             $guild
 * @property \Illuminate\Database\Eloquent\Collection|\App\Signup[] $signups
 * @mixin \Eloquent
 *
 * @property int                             $id
 * @property string                          $name
 * @property string                          $description
 * @property string                          $start_date
 * @property int                             $guild_id
 * @property int|null                        $parent_repeatable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int                             $locked
 * @property array                           $tags
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereGuildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereParentRepeatable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereUpdatedAt($value)
 */
class Event extends \Illuminate\Database\Eloquent\Model
{
    const EVENT_STATUS_OPEN = 0;

    const EVENT_STATUS_LOCKED = 1;

    protected $fillable = [
        'name',
        'guild_id',
        'description',
        'start_date',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    protected $logger;

    public function __construct(array $attributes = [])
    {
        $this->logger = new GuildLogger();
        parent::__construct($attributes);
    }

    /**
     * Delete the event.
     *
     * @return bool|null
     *
     * @throws \Exception
     */
    public function delete()
    {
        $this->signups()->delete();
        $this->sendDeletionNotifications();

        return parent::delete();
    }

    /**
     * Get all signups for the event.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function signups()
    {
        return $this->hasMany('App\Signup');
    }

    /**
     * Get all signups for the event by role.
     *
     * @param int $role
     *
     * @return array
     */
    public function signupsByRole(int $role)
    {
        return $this->signups()->where('role_id', '=', $role)->get()->all() ?? [];
    }

    /**
     * Get all comments for the event.
     *
     * @return array
     */
    public function comments()
    {
        return $this->hasMany('App\Comment')->orderBy(Comment::CREATED_AT, 'desc')->get()->all();
    }

    /**
     * Get the guild this event belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function guild()
    {
        return $this->belongsTo('App\Guild');
    }

    /**
     * Get the event tags.
     *
     * @return array
     */
    public function tags(): array
    {
        return $this->tags ?? [];
    }

    public function startDate(string $timeZone = 'UTC'): \DateTime
    {
        return UserDateHandler::getDateTime(new \DateTime($this->start_date), $timeZone);
    }

    /**
     * Check if the event is locked.
     *
     * @return bool
     */
    public function locked(): bool
    {
        return self::EVENT_STATUS_LOCKED === $this->locked;
    }

    /**
     * Get a human readable date string based on user settings.
     *
     * @return string
     */
    public function getUserHumanReadableDate($timezone = 'UTC', $clock = 24): string
    {
        return UserDateHandler::getUserHumanReadableDate(new \DateTime($this->start_date), $timezone, $clock);
    }

    public function lock(int $status)
    {
        $this->locked = $status;
        $this->save();
    }

    /**
     * Sign a user up for this event.
     *
     * @param User  $user
     * @param int   $class
     * @param int   $role
     * @param array $sets
     */
    public function signup(User $user, int $class, int $role, array $sets = [])
    {
        if ($this->isSignedUp($user)) {
            $signup = $this->signups()
                ->where('user_id', '=', $user->id)
                ->first();
            $signup->update([
                'event_id' => $this->id,
                'user_id'  => $user->id,
                'class_id' => $class,
                'role_id'  => $role,
                'sets'     => $sets,
            ]);
        } else {
            $signup = new Signup([
                'event_id' => $this->id,
                'user_id'  => $user->id,
                'class_id' => $class,
                'role_id'  => $role,
                'sets'     => $sets,
            ]);
            $this->logger->eventSignup($this, $user);
        }
        $signup->save();
    }

    /**
     * Sign a user up for this event using a character preset.
     *
     * @param User      $user
     * @param Character $character
     */
    public function signupWithCharacter(User $user, Character $character)
    {
        if ($this->isSignedUp($user)) {
            $signup = $this->signups()
                ->where('user_id', '=', $user->id)
                ->first();
            $signup->update([
                'event_id'     => $this->id,
                'user_id'      => $user->id,
                'class_id'     => $character->class,
                'role_id'      => $character->role,
                'sets'         => $character->sets(),
                'character_id' => $character->id,
            ]);
        } else {
            $signup = new Signup([
                'event_id'     => $this->id,
                'user_id'      => $user->id,
                'class_id'     => $character->class,
                'role_id'      => $character->role,
                'sets'         => $character->sets(),
                'character_id' => $character->id,
            ]);
            $this->logger->eventSignup($this, $user);
        }
        $signup->save();
    }

    public function signupWithTeam(Team $team)
    {
        foreach ($team->users() as $user) {
            $this->signup($user, $user->class_id, $user->role_id, json_decode($user->sets, true) ?? []);
        }
    }

    /**
     * Sign a user off for this event.
     *
     * @param User $user
     */
    public function signoff(User $user)
    {
        Signup::query()
            ->where('user_id', '=', $user->id)
            ->where('event_id', '=', $this->id)
            ->delete();
        $this->logger->eventSignoff($this, $user);
    }

    /**
     * Check if the user is signed up for this event.
     *
     * @param User $user
     *
     * @return bool
     */
    public function isSignedUp(User $user): bool
    {
        return 1 === $this->signups()
                ->where('user_id', '=', $user->id)
                ->count();
    }

    public function getSignup(User $user): ?Signup
    {
        return $this->signups()
            ->where('user_id', '=', $user->id)
            ->first();
    }

    public function addComment(User $user, string $text)
    {
        $comment = new Comment([
            'user_id'  => $user->id,
            'event_id' => $this->id,
            'text'     => $text,
        ]);
        $comment->save();
    }

    public function updateComment(Comment $comment, string $text)
    {
        if ($comment->event_id === $this->id) {
            $comment->text = $text;
            $comment->save();
        }
    }

    public function deleteComment(Comment $comment)
    {
        if ($comment->event_id === $this->id) {
            $comment->delete();
        }
    }

    public function sendCreationNotifications()
    {
        $notifications = Notification::query()
            ->where('call_type', '=', EventCreationMessage::CALL_TYPE)
            ->where('guild_id', '=', $this->guild_id)
            ->get()->all();

        /** @var Notification $notification */
        foreach ($notifications as $notification) {
            $notification->send(['event' => $this]);
        }
    }

    public function sendDeletionNotifications()
    {
        $notifications = Notification::query()
            ->where('call_type', '=', EventDeletionMessage::CALL_TYPE)
            ->where('guild_id', '=', $this->guild_id)
            ->get()->all();

        /** @var Notification $notification */
        foreach ($notifications as $notification) {
            $notification->send(['event' => $this]);
        }
    }

    public function sendSignupsNotifications()
    {
        $notifications = Notification::query()
            ->where('call_type', '=', PostSignupsMessage::CALL_TYPE)
            ->where('guild_id', '=', $this->guild_id)
            ->get()->all();

        /** @var Notification $notification */
        foreach ($notifications as $notification) {
            $notification->send(['event' => $this]);
        }
    }

    public function sendReminderNotifications()
    {
        $notifications = Notification::query()
            ->where('call_type', '=', ReminderMessage::CALL_TYPE)
            ->where('guild_id', '=', $this->guild_id)
            ->get()->all();

        /** @var Notification $notification */
        foreach ($notifications as $notification) {
            $notification->send(['event' => $this]);
        }
    }

    public function getViewRoute(): string
    {
        return route('eventDetailView', ['slug' => $this->guild->slug, 'event_id' => $this->id]);
    }
}
