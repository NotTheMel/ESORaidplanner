<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * App\User.
 *
 * @property \Illuminate\Database\Eloquent\Collection|\App\Character[]                                                 $characters
 * @property \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property \Illuminate\Database\Eloquent\Collection|\App\Signup[]                                                    $signups
 * @mixin \Eloquent
 *
 * @property int                             $id
 * @property string                          $name
 * @property string                          $email
 * @property string                          $password
 * @property string                          $timezone
 * @property string                          $avatar
 * @property int                             $global_admin
 * @property int                             $membership_level
 * @property string|null                     $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int                             $clock
 * @property int                             $layout
 * @property string|null                     $telegram_username
 * @property string                          $cover_image
 * @property string                          $title
 * @property string                          $description
 * @property int                             $race
 * @property int                             $alliance
 * @property int                             $class
 * @property mixed|null                      $onesignal_id
 * @property int                             $nightmode
 * @property string|null                     $discord_handle
 * @property string|null                     $discord_id
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereAlliance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereClock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCoverImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereDiscordHandle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereDiscordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereGlobalAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereLayout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereMembershipLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereNightmode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereOnesignalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereTelegramUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'timezone', 'clock', 'layout', 'telegram_username', 'discord_handle', 'race', 'alliance', 'class', 'title', 'description',
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
     * Get all signups for this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function signups()
    {
        return $this->hasMany('App\Signup');
    }

    public function characters()
    {
        return $this->hasMany('App\Character');
    }

    /**
     * Get all guilds for this user.
     *
     * @return array
     */
    public function guilds()
    {
        return Guild::query()
            ->select('guilds.*')
            ->join(Guild::X_REF_USERS, 'guilds.id', Guild::X_REF_USERS.'.guild_id')
            ->where(Guild::X_REF_USERS.'.user_id', '=', $this->id)
            ->where(Guild::X_REF_USERS.'.status', '=', Guild::MEMBERSHIP_STATUS_MEMBER)
            ->orderBy('guilds.name')
            ->get()->all();
    }

    public function guildsPending()
    {
        return Guild::query()
            ->select('guilds.*')
            ->join(Guild::X_REF_USERS, 'guilds.id', Guild::X_REF_USERS.'.guild_id')
            ->where(Guild::X_REF_USERS.'.user_id', '=', $this->id)
            ->where(Guild::X_REF_USERS.'.status', '=', Guild::MEMBERSHIP_STATUS_PENDING)
            ->orderBy('guilds.name')
            ->get()->all();
    }

    public function guildsWhereIsAdmin()
    {
        return Guild::query()
            ->select('guilds.*')
            ->join(Guild::X_REF_USERS, 'guilds.id', Guild::X_REF_USERS.'.guild_id')
            ->where(Guild::X_REF_USERS.'.user_id', '=', $this->id)
            ->whereJsonContains('guilds.admins', $this->id)
            ->orWhere(Guild::X_REF_USERS.'.user_id', '=', $this->id)
            ->where('guilds.admins', 'LIKE', '"'.$this->id.'"')
            ->orderBy('guilds.name')
            ->get()->all();
    }

    /**
     * Get all upcoming events for this user.
     *
     * @return array
     */
    public function UpcomingEvents(): array
    {
        $guilds = $this->guilds();

        $events = [];

        /** @var Guild $guild */
        foreach ($guilds as $guild) {
            foreach ($guild->upcomingEvents() as $event) {
                $events[] = $event;
            }
        }

        usort($events, function ($a, $b) {
            return $a->start_date > $b->start_date;
        });

        return $events;
    }

    public function currentLocalDateTime(): \DateTime
    {
        $dt = new \DateTime('now', new \DateTimeZone(env('DEFAULT_TIMEZONE')));
        $dt->setTimezone(new \DateTimeZone($this->timezone));

        return $dt;
    }

    public function getDiscordMention(): string
    {
        if (null === $this->discord_id) {
            return $this->name;
        }

        return '<@'.$this->discord_id.'>';
    }

    public function createIcalUid(): string
    {
        return base64_encode($this->id.'|'.$this->created_at);
    }
}
