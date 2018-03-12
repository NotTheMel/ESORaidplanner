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

/**
 * Class Comment.
 */
class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'event_id',
        'text',
    ];

    /**
     * @return string
     */
    public function getNiceDate(): string
    {
        $date = new DateTime($this->created_at);

        $date->setTimezone(new DateTimeZone(Auth::user()->timezone));

        return $date->format('F jS H:i');
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        $user = User::query()
            ->where('id', '=', $this->user_id)
            ->first();

        return $user->name;
    }

    /**
     * @return string
     */
    public function getUserAvatar(): string
    {
        $user = User::query()
            ->where('id', '=', $this->user_id)
            ->first();

        return $user->avatar;
    }
}
