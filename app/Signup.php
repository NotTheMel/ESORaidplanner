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

use App\Singleton\ClassTypes;
use App\Singleton\RoleTypes;
use DateTime;
use DateTimeZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Signup extends Model
{
    const SIGNUP_STATUS_CONFIRMED = 1;
    const SIGNUP_STATUS_BACKUP    = 2;

    protected $fillable = [
        'user_id',
        'event_id',
        'class_id',
        'role_id',
        'sets',
        'character_id',
        'status',
    ];

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
     * @return User
     */
    public function getUser(): User
    {
        return User::query()
            ->where('id', '=', $this->user_id)
            ->first();
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return ClassTypes::getClassName($this->class_id);
    }

    public function getClassIcon(): string
    {
        return ClassTypes::getClassIcon($this->class_id);
    }

    /**
     * @return string
     */
    public function getRoleName(): string
    {
        return RoleTypes::getRoleName($this->role_id);
    }

    /**
     * @return string
     */
    public function getSetsFormatted(): string
    {
        if (empty($this->sets)) {
            return '';
        }
        $sets = explode(', ', $this->sets);

        $setsFound    = [];
        $setsNotFound = [];

        $string = ''
;
        foreach ($sets as $set) {
            $new = Set::query()->whereRaw('LOWER(`name`) LIKE "%'.strtolower($set).'%"')->first();
            if (!empty($new)) {
                $setsFound[] = $new;
            } else {
                $setsNotFound[] = $set;
            }
        }

        foreach ($setsFound as $set) {
            $string .= '<a href="https://www.eso-sets.com/set/'.$set->id.'" target="_blank" data-toggle="tooltip" data-html="true" title="'.$set->tooltip.'">'.$set->name.'</a>, ';
        }

        $string .= implode(', ', $setsNotFound);

        if (', ' === substr($string, -2, 2)) {
            $string = substr($string, 0, -2);
        }

        return $string;
    }
}
