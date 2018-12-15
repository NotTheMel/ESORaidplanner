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
 * Date: 12.09.18
 * Time: 08:43
 */

namespace App;

use App\Utility\Classes;
use App\Utility\Roles;
use App\Utility\UserDateHandler;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Signup.
 *
 * @property \App\Event $event
 * @property \App\User  $user
 * @mixin \Eloquent
 *
 * @property int                             $id
 * @property int                             $user_id
 * @property int                             $event_id
 * @property int                             $class_id
 * @property int                             $role_id
 * @property mixed|null                      $sets
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null                        $character_id
 * @property int                             $status
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Signup whereCharacterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Signup whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Signup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Signup whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Signup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Signup whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Signup whereSets($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Signup whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Signup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Signup whereUserId($value)
 */
class Signup extends Model
{
    const STATUS_CONFIRMED = 1;
    const STATUS_BACKUP    = 2;
    const STATUS_UNKNOWN   = 0;

    protected $fillable = [
        'event_id',
        'user_id',
        'class_id',
        'role_id',
        'sets',
        'character_id',
    ];

    protected $casts = [
        'sets' => 'array',
    ];

    /**
     * Get the event this signup belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo('App\Event');
    }

    /**
     * Get the user this signup belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function class(): string
    {
        return Classes::CLASSES[$this->class_id];
    }

    public function role(): string
    {
        return Roles::ROLES[$this->role_id];
    }

    public function classIcon()
    {
        return Classes::getClassIcon($this->class_id);
    }

    public function roleIcon()
    {
        return Roles::getRoleIcon($this->role_id);
    }

    /**
     * Get a human readable date string based on user settings.
     *
     * @return string
     */
    public function getUserHumanReadableDate(): string
    {
        return UserDateHandler::getUserHumanReadableDate(new \DateTime($this->{self::CREATED_AT}));
    }

    /**
     * @return array
     */
    public function getSets(): array
    {
        return $this->sets ?? [];
    }

    public function setStatus(int $status)
    {
        $this->status = $status;
        $this->save();
    }

    public function getStatusIcon(): string
    {
        switch ($this->status) {
            case self::STATUS_CONFIRMED:
                return '✅';
            case self::STATUS_BACKUP:
                return '⚠️';

            default:
                return '❔';
        }
    }
}
