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

namespace App\Hook;

use App\Event;

class ReminderNotification extends NotificationHook
{
    public function call(Event $event)
    {
        $this->send($this->buildMessage($event));

        $this->wasCalled($event);
    }

    private function buildMessage(Event $event)
    {
        $guild = $event->getGuild();

        $message = str_replace(['{EVENT_NAME}', '{EVENT_DESCRIPTION}', '{EVENT_NUM_SIGNUPS}', '{EVENT_URL}'], [$event->name, $event->description, $event->getTotalSignups(), 'https://esoraidplanner.com/g/'.$guild->slug.'/event/'.$event->id], $this->message);

        return $message;
    }
}
