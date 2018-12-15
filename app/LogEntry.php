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

use App\Utility\UserDateHandler;
use DateTime;
use Illuminate\Database\Eloquent\Model;

/**
 * App\LogEntry.
 *
 * @property \App\Guild $guild
 * @mixin \Eloquent
 *
 * @property int                             $id
 * @property int                             $guild_id
 * @property string                          $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LogEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LogEntry whereGuildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LogEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LogEntry whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LogEntry whereUpdatedAt($value)
 */
class LogEntry extends Model
{
    protected $table    = 'guildlogs';
    protected $fillable = [
        'guild_id',
        'message',
    ];

    public function guild()
    {
        return $this->belongsTo('App\Guild');
    }

    /**
     * @param int    $guild_id
     * @param string $message
     */
    public function create(int $guild_id, string $message)
    {
        $this->guild_id = $guild_id;
        $this->message  = $message;
        $this->save();
    }

    /**
     * @return string
     */
    public function getUserHumanReadableDate(): string
    {
        return UserDateHandler::getUserHumanReadableDate(new DateTime($this->{self::CREATED_AT}));
    }
}
