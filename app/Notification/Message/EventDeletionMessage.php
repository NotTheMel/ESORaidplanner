<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 15.10.18
 * Time: 09:25.
 */

namespace App\Notification\Message;

use App\Notification\Notification;
use App\Notification\System\AbstractNotificationSystem;
use Woeler\DiscordPhp\Message\DiscordEmbedsMessage;

class EventDeletionMessage extends AbstractNotificationMessage
{
    const CALL_TYPE = 5;
    const IDEN      = 'event.deleted';
    const CONFIG    = [
        'identifier'  => 'event.deleted',
        'name'        => 'Event deletion notification',
        'description' => 'Sends a notification every time an event is deleted.',
    ];

    public function meetsSendingConditions(Notification $notification): bool
    {
        if (!$this->hasNeededSubjects()) {
            return false;
        }

        if (!$this->matchesTags($notification->tags(), $this->subjects['event']->tags())) {
            return false;
        }

        return true;
    }

    protected function buildDiscordEmbeds(): DiscordEmbedsMessage
    {
        $embeds = new DiscordEmbedsMessage();
        $embeds->setUsername('ESO Raidplanner');
        $embeds->setAvatar('https://esoraidplanner.com'.AbstractNotificationSystem::AVATAR_URL);
        $embeds->setTitle('Event '.$this->subjects['event']->name.' has been deleted');
        $embeds->setColor(9660137);
        $embeds->setAuthorName('ESO Raidplanner');
        $embeds->setAuthorIcon('https://esoraidplanner.com/favicon/appicon.jpg');
        $embeds->setAuthorUrl('https://esoraidplanner.com');
        $embeds->setFooterIcon('https://esoraidplanner.com/favicon/appicon.jpg');
        $embeds->setFooterText('ESO Raidplanner by Woeler');

        return $embeds;
    }

    protected function buildText(): string
    {
        return str_replace(['{EVENT_NAME}', '{EVENT_DESCRIPTION}', '{EVENT_NUM_SIGNUPS}'], [$this->subjects['event']->name, $this->subjects['event']->description, $this->subjects['event']->signups()->count()], $this->text);
    }

    protected function hasNeededSubjects(): bool
    {
        return array_key_exists('event', $this->subjects);
    }
}
