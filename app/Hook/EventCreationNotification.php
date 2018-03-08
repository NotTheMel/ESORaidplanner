<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 07.03.18
 * Time: 08:57.
 */

namespace App\Hook;

use App\Event;

class EventCreationNotification extends NotificationHook
{
    public function call(Event $event)
    {
        $this->send($this->buildMessage($event));
    }

    private function buildMessage(Event $event)
    {
        $guild = $event->getGuild();

        $message = str_replace('{EVENT_NAME}', $event->name, $this->message);
        $message = str_replace('{EVENT_DESCRIPTION}', $event->description, $message);
        $message = str_replace('{EVENT_NUM_SIGNUPS}', $event->getTotalSignups(), $message);
        $message = str_replace('{EVENT_URL}', 'https://esoraidplanner.com/g/'.$guild->slug.'/event/'.$event->id, $message);

        return $message;
    }
}
