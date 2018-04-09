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
use App\Singleton\ClassTypes;
use App\Singleton\RoleTypes;
use App\Singleton\SignupStatusses;
use App\User;

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

        $signups = $event->getSignups(SignupStatusses::STATUS_CONFIRMED);

        $message = str_replace(['{EVENT_NAME}', '{EVENT_DESCRIPTION}', '{EVENT_NUM_SIGNUPS}', '{EVENT_URL}'], [$event->name, $event->description, $event->getTotalSignups(), 'https://esoraidplanner.com/g/'.$guild->slug.'/event/'.$event->id], $this->message);

        if (strpos($message, '{CONFIRMED_SIGNUPS}') !== false){
            $m = '```';

            foreach ($signups as $signup){
                $u = User::query()->find($signup->user_id);
                $m .= $u->name . ' - ' . ClassTypes::getClassName($signup->class_id) . ' - ' . RoleTypes::getRoleName($signup->role_id) . PHP_EOL;
            }
            $m .= '```';

            $message = str_replace('{CONFIRMED_SIGNUPS}', $m, $message);
        }

        return $message;
    }
}
