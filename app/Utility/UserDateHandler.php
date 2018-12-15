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
 * Time: 15:13
 */

namespace App\Utility;

use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Auth;

class UserDateHandler
{
    public static function getUserHumanReadableDate(DateTime $dateTime, string $timeZone = 'UTC', int $clock = 24): string
    {
        $dateTime->setTimezone(new DateTimeZone(Auth::user()->timezone ?? $timeZone));

        $clock = Auth::user()->clock ?? $clock;

        if (12 === $clock) {
            return $dateTime->format('F jS g:i a');
        }

        return $dateTime->format('F jS H:i');
    }

    public static function getDateTime(DateTime $dateTime, string $timeZone = 'UTC'): DateTime
    {
        $dateTime->setTimezone(new DateTimeZone($timeZone));

        return $dateTime;
    }

    public static function timeZones(): array
    {
        static $regions = [
            DateTimeZone::AFRICA,
            DateTimeZone::AMERICA,
            DateTimeZone::ANTARCTICA,
            DateTimeZone::ASIA,
            DateTimeZone::ATLANTIC,
            DateTimeZone::AUSTRALIA,
            DateTimeZone::EUROPE,
            DateTimeZone::INDIAN,
            DateTimeZone::PACIFIC,
        ];

        $timezones = [];
        foreach ($regions as $region) {
            $timezones = array_merge($timezones, DateTimeZone::listIdentifiers($region));
        }

        $timezone_offsets = [];
        foreach ($timezones as $timezone) {
            $tz                          = new DateTimeZone($timezone);
            $timezone_offsets[$timezone] = $tz->getOffset(new DateTime());
        }

        // sort timezone by offset
        asort($timezone_offsets);

        $timezone_list = [];
        foreach ($timezone_offsets as $timezone => $offset) {
            $offset_prefix    = $offset < 0 ? '-' : '+';
            $offset_formatted = gmdate('H:i', abs($offset));

            $pretty_offset = "UTC${offset_prefix}${offset_formatted}";

            $timezone_list[$timezone] = "(${pretty_offset}) $timezone";
        }

        return $timezone_list;
    }

    public static function requestToDateTime(array $request, int $clock, DateTimeZone $timeZone): DateTime
    {
        if (1 === \strlen($request['hour'])) {
            $hour = '0'.$request['hour'];
        } else {
            $hour = $request['hour'];
        }

        if (1 === \strlen($request['minute'])) {
            $minute = '0'.$request['minute'];
        } else {
            $minute = $request['minute'];
        }

        if (12 === $clock) {
            $date = new DateTime($request['year'].'-'.$request['month'].'-'.$request['day'].' '.$hour.':'.$minute.' '.$request['meridiem'], $timeZone);
        } else {
            $date = new DateTime($request['year'].'-'.$request['month'].'-'.$request['day'].' '.$hour.':'.$minute, $timeZone);
        }

        $date->setTimezone(new DateTimeZone(env('DEFAULT_TIMEZONE')));

        return $date;
    }
}
