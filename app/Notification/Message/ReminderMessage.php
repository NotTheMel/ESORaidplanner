<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 13.10.18
 * Time: 15:43.
 */

namespace App\Notification\Message;

use App\Notification\Notification;
use App\Notification\System\AbstractNotificationSystem;
use DateTime;
use Illuminate\Support\Facades\DB;
use Woeler\DiscordPhp\Message\DiscordEmbedsMessage;

class ReminderMessage extends AbstractNotificationMessage
{
    const CALL_TYPE = 2;
    const IDEN      = 'event.reminder';
    const CONFIG    = [
        'identifier'  => 'event.reminder',
        'name'        => 'Event reminder notification',
        'description' => 'Sends a notification at a specified time before event start.',
    ];

    protected $isTimed = true;

    public function meetsSendingConditions(Notification $notification): bool
    {
        if (!$this->hasNeededSubjects()) {
            return false;
        }

        $alreadySent = 0 !== DB::table('hookcalls')->where([
                'hook_id'  => $notification->id,
                'event_id' => $this->subjects['event']->id,
            ])->count();

        if ($alreadySent) {
            return false;
        }

        if (!$this->matchesTags($notification->tags(), $this->subjects['event']->tags())) {
            return false;
        }

        $start_time = new DateTime($this->subjects['event']->start_date);
        $now        = new DateTime();
        if ($now->getTimestamp() < ($start_time->getTimestamp() - $notification->call_time_diff)) {
            return false;
        }
        if (0 !== $notification->if_less_signups && null !== $notification->if_less_signups && $notification->if_less_signups < $this->subjects['event']->signups()->count()) {
            return false;
        }
        if (0 !== $notification->if_more_signups && null !== $notification->if_more_signups && $notification->if_more_signups > $this->subjects['event']->signups()->count()) {
            return false;
        }

        return true;
    }

    public function wasCalled(Notification $notification)
    {
        DB::table('hookcalls')->insert([
            'hook_id'    => $notification->id,
            'event_id'   => $this->subjects['event']->id,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    protected function buildDiscordEmbeds(): DiscordEmbedsMessage
    {
        $embeds = new DiscordEmbedsMessage();
        $embeds->setUsername('ESO Raidplanner');
        $embeds->setAvatar('https://esoraidplanner.com'.AbstractNotificationSystem::AVATAR_URL);
        $embeds->setTitle('Reminder for '.$this->subjects['event']->name);
        $embeds->setUrl($this->subjects['event']->getViewRoute());
        $embeds->setDescription($this->getText());
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
        return str_replace(['{EVENT_NAME}', '{EVENT_DESCRIPTION}', '{EVENT_NUM_SIGNUPS}', '{EVENT_URL}'], [$this->subjects['event']->name, $this->subjects['event']->description, $this->subjects['event']->signups()->count(), $this->subjects['event']->getViewRoute()], $this->text);
    }

    protected function hasNeededSubjects(): bool
    {
        return array_key_exists('event', $this->subjects);
    }
}
