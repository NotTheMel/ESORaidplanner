<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 23.04.18
 * Time: 09:31
 */

namespace App\Hook;


use App\Event;
use App\Singleton\ClassTypes;
use App\Singleton\RoleTypes;
use App\Singleton\SignupStatusses;
use App\User;

class ConfirmedSignupsNotification extends NotificationHook
{
    public function call(Event $event)
    {
        $this->send($this->buildMessage($event));
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