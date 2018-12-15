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

namespace App\Http\Controllers\Api\Ical;

use App\Guild;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Response;
use Jsvrcek\ICS\CalendarExport;
use Jsvrcek\ICS\CalendarStream;
use Jsvrcek\ICS\Model\Calendar;
use Jsvrcek\ICS\Model\CalendarEvent;
use Jsvrcek\ICS\Utility\Formatter;

class IcalController extends Controller
{
    public function user(string $uid)
    {
        $data = explode('|', base64_decode($uid));

        if (!mb_detect_encoding(base64_decode($uid), 'ASCII', true)) {
            return \response('Not found', Response::HTTP_NOT_FOUND);
        }

        /** @var User $user */
        $user = User::query()->where('id', '=', $data[0])
        ->where('created_at', '=', $data[1])
        ->first();

        if (null === $user) {
            return \response('Not found', Response::HTTP_NOT_FOUND);
        }

        $calendar = new Calendar();
        $calendar->setProdId('-//ESO Raidplanner//'.$user->name.'//EN');
        $calendar->setTimezone(new \DateTimeZone(env('DEFAULT_TIMEZONE')));

        foreach ($user->guilds() as $guild) {
            $events = $guild->events;

            foreach ($events as $event) {
                $calEvent = new CalendarEvent();
                $calEvent->setStart(new \DateTime($event->start_date))
                    ->setSummary($event->name.' ('.$guild->name.')')
                    ->setDescription($event->description ?? '')
                    ->setUid($event->id);
                $calendar->addEvent($calEvent);
            }
        }

        $calendarExport = new CalendarExport(new CalendarStream(), new Formatter());
        $calendarExport->addCalendar($calendar);

        return \response($calendarExport->getStream(), Response::HTTP_OK);
    }

    public function guild(string $uid)
    {
        $data = explode('|', base64_decode($uid));

        if (!mb_detect_encoding(base64_decode($uid), 'ASCII', true)) {
            return \response('Not found', Response::HTTP_NOT_FOUND);
        }

        $user = User::query()->find($data[0]);
        /** @var Guild $guild */
        $guild = Guild::query()
        ->where('id', '=', $data[1])
        ->where('created_at', '=', $data[2])
        ->first();

        if (null === $guild || null === $user || !$guild->isMember($user)) {
            return \response('Not found', Response::HTTP_NOT_FOUND);
        }

        $events = $guild->events;

        $calendar = new Calendar();
        $calendar->setProdId('-//ESO Raidplanner//'.$guild->name.'//EN');
        $calendar->setTimezone(new \DateTimeZone(env('DEFAULT_TIMEZONE')));

        foreach ($events as $event) {
            $calEvent = new CalendarEvent();
            $calEvent->setStart(new \DateTime($event->start_date))
                ->setSummary($event->name)
                ->setDescription($event->description ?? '')
                ->setUid($event->id);
            $calendar->addEvent($calEvent);
        }

        $calendarExport = new CalendarExport(new CalendarStream(), new Formatter());
        $calendarExport->addCalendar($calendar);

        return \response($calendarExport->getStream(), Response::HTTP_OK);
    }
}
