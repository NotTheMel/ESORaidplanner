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
 * Time: 20:38
 */

namespace App;

use App\Utility\EventTimeIntervals;
use DateTime;
use DateTimeZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * App\RepeatableEvent.
 *
 * @mixin \Eloquent
 *
 * @property int                             $id
 * @property string                          $name
 * @property string                          $description
 * @property int                             $interval
 * @property string                          $latest_event
 * @property int                             $type
 * @property int                             $guild_id
 * @property array                           $tags
 * @property int                             $create_interval
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null                        $default_team_id
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RepeatableEvent whereCreateInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RepeatableEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RepeatableEvent whereDefaultTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RepeatableEvent whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RepeatableEvent whereGuildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RepeatableEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RepeatableEvent whereInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RepeatableEvent whereLatestEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RepeatableEvent whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RepeatableEvent whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RepeatableEvent whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RepeatableEvent whereUpdatedAt($value)
 */
class RepeatableEvent extends Model
{
    protected $table = 'recurring';

    protected $fillable = ['name', 'description', 'interval', 'latest_event', 'guild_id', 'create_interval', 'default_team_id', 'timezone'];

    protected $casts = [
        'tags' => 'array',
    ];

    public function guild()
    {
        return $this->hasOne('App\Guild');
    }

    public function latestEvent(): ?Event
    {
        return Event::query()->find($this->latest_event);
    }

    public function getRepetitionString(): string
    {
        $dt = new DateTime($this->latest_event_date);
        $dt->setTimezone(new DateTimeZone(Auth::user()->timezone));

        return $dt->format('l').' '.EventTimeIntervals::INTERVALS[$this->interval];
    }

    public function tags(): array
    {
        return $this->tags ?? [];
    }
}
