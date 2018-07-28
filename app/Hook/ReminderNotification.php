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
        $this->send($this->buildMessage($event), $event->buildDiscordEmbeds());

        $this->wasCalled($event);
    }

    private function buildMessage(Event $event)
    {
        $guild = $event->getGuild();

        $message = str_replace(['{EVENT_NAME}', '{EVENT_DESCRIPTION}', '{EVENT_NUM_SIGNUPS}', '{EVENT_URL}'], [$event->name, $event->description, $event->getTotalSignups(), 'https://esoraidplanner.com/g/'.$guild->slug.'/event/'.$event->id], $this->message);

        if (false !== strpos($message, '{CONFIRMED_SIGNUPS}')) {
            $signups = $event->getSignupsOrderedByRole(SignupStatusses::STATUS_CONFIRMED);
            if (count($signups) > 0) {
                if (NotificationHook::TYPE_DISCORD === $this->type || NotificationHook::TYPE_SLACK === $this->type) {
                    $m = '```';
                } else {
                    $m = '';
                }

                $m .= 'CONFIRMED:'.PHP_EOL;

                foreach ($signups as $signup) {
                    $u = User::query()->find($signup->user_id);
                    $m .= $u->name.' - '.ClassTypes::getClassName($signup->class_id).' - '.RoleTypes::getRoleName($signup->role_id).PHP_EOL;
                }
                if (NotificationHook::TYPE_DISCORD === $this->type || NotificationHook::TYPE_SLACK === $this->type) {
                    $m .= '```';
                }

                $message = str_replace('{CONFIRMED_SIGNUPS}', $m, $message);
            } else {
                $message = str_replace('{CONFIRMED_SIGNUPS}', '', $message);
            }
        }

        if (false !== strpos($message, '{BACKUP_SIGNUPS}')) {
            $signups = $event->getSignupsOrderedByRole(SignupStatusses::STATUS_BACKUP);
            if (count($signups) > 0) {
                if (NotificationHook::TYPE_DISCORD === $this->type || NotificationHook::TYPE_SLACK === $this->type) {
                    $m = '```';
                } else {
                    $m = '';
                }

                $m .= 'BACKUP:'.PHP_EOL;

                foreach ($signups as $signup) {
                    $u = User::query()->find($signup->user_id);
                    $m .= $u->name.' - '.ClassTypes::getClassName($signup->class_id).' - '.RoleTypes::getRoleName($signup->role_id).PHP_EOL;
                }
                if (NotificationHook::TYPE_DISCORD === $this->type || NotificationHook::TYPE_SLACK === $this->type) {
                    $m .= '```';
                }

                $message = str_replace('{BACKUP_SIGNUPS}', $m, $message);
            } else {
                $message = str_replace('{BACKUP_SIGNUPS}', '', $message);
            }
        }

        return $message;
    }
}
