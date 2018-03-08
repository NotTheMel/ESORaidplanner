<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 07.03.18
 * Time: 08:35.
 */

namespace App\Builder;

use App\Event;

class HookMessageBuilder
{
    public static function buildEventReminderMessage(string $message, Event $event): string
    {
        $guild = $event->getGuild();

        $message = str_replace('{EVENT_NAME}', $event->name, $message);
        $message = str_replace('{EVENT_DESCRIPTION}', $event->description, $message);
        $message = str_replace('{EVENT_NUM_SIGNUPS}', $event->getTotalSignups(), $message);
        $message = str_replace('{EVENT_URL}', 'https://esoraidplanner.com/g/'.$guild->slug.'/event/'.$event->id, $message);

        return $message;
    }
}
