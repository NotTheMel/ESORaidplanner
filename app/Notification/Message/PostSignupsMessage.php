<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 13.10.18
 * Time: 16:08.
 */

namespace App\Notification\Message;

use App\Event;
use App\Notification\System\AbstractNotificationSystem;
use App\Signup;
use App\Utility\Roles;
use Woeler\DiscordPhp\Message\DiscordEmbedsMessage;

class PostSignupsMessage extends AbstractNotificationMessage
{
    const CALL_TYPE = 4;
    const IDEN      = 'event.signups.post';
    const CONFIG    = [
        'identifier'  => 'event.signups.post',
        'name'        => 'Post signups / roster notification',
        'description' => 'Sends a notification every the post signups button on an event is pressed.',
    ];

    public function hasNeededSubjects(): bool
    {
        return array_key_exists('event', $this->subjects);
    }

    protected function buildDiscordEmbeds(): DiscordEmbedsMessage
    {
        $embeds = new DiscordEmbedsMessage();
        $embeds->setUsername('ESO Raidplanner');
        $embeds->setAvatar('https://esoraidplanner.com'.AbstractNotificationSystem::AVATAR_URL);
        $embeds->setTitle('Event '.$this->subjects['event']->name.' roster');
        $embeds->setDescription($this->subjects['event']->description ?? '');
        $embeds->setUrl($this->subjects['event']->getViewRoute());
        $embeds->setColor(9660137);
        $embeds->setAuthorName('ESO Raidplanner');
        $embeds->setAuthorIcon('https://esoraidplanner.com/favicon/appicon.jpg');
        $embeds->setAuthorUrl('https://esoraidplanner.com');
        $embeds->setFooterIcon('https://esoraidplanner.com/favicon/appicon.jpg');
        $embeds->setFooterText('ESO Raidplanner by Woeler');

        /** @var Event $event */
        $event = $this->subjects['event'];
        foreach (Roles::ROLES as $role_id => $role_name) {
            if (empty($event->signupsByRole($role_id))) {
                continue;
            }
            $text = '';
            /** @var Signup $signup */
            foreach ($event->signupsByRole($role_id) as $signup) {
                $text .= $signup->getStatusIcon().' '.$signup->user->getDiscordMention().PHP_EOL;
            }

            $embeds->addField($role_name, $text, true);
        }

        return $embeds;
    }

    protected function buildText(): string
    {
        $message = str_replace(['{EVENT_NAME}', '{EVENT_DESCRIPTION}', '{EVENT_NUM_SIGNUPS}', '{EVENT_URL}'], [$this->subjects['event']->name, $this->subjects['event']->description, $this->subjects['event']->signups()->count(), $this->subjects['event']->getViewRoute()], $this->text ?? '').PHP_EOL;

        /** @var Event $event */
        $event   = $this->subjects['event'];
        $message .= 'Roster for '.$event->name.PHP_EOL;
        foreach (Roles::ROLES as $role_id => $role_name) {
            if (empty($event->signupsByRole($role_id))) {
                continue;
            }
            $message .= $role_name.PHP_EOL;
            /** @var Signup $signup */
            foreach ($event->signupsByRole($role_id) as $signup) {
                $message .= $signup->getStatusIcon().' '.$signup->user->name.PHP_EOL;
            }
            $message .= PHP_EOL;
        }

        return $message;
    }
}
