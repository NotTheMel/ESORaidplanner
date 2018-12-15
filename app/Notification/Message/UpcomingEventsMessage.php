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

namespace App\Notification\Message;

use App\Notification\Notification;
use App\Notification\System\AbstractNotificationSystem;
use Illuminate\Support\Facades\DB;
use Woeler\DiscordPhp\Message\DiscordEmbedsMessage;

class UpcomingEventsMessage extends AbstractNotificationMessage
{
    const CALL_TYPE = 6;
    const IDEN      = 'guild.events.upcoming';
    const CONFIG    = [
        'identifier'  => 'guild.events.upcoming',
        'name'        => 'Daily upcoming events notification',
        'description' => 'Sends a daily notification containing all upcoming events.',
    ];

    protected $isTimed = true;

    public function meetsSendingConditions(Notification $notification): bool
    {
        if (!$this->hasNeededSubjects()) {
            return false;
        }

        $now        = new \DateTime();
        $timeToCall = new \DateTime(date('Y-m-d').' '.$notification->daily_trigger_time.':00');

        if ($timeToCall > $now) {
            return false;
        }

        $dt = new \DateTime('now', new \DateTimeZone(env('DEFAULT_TIMEZONE')));

        $alreadySent = 0 !== DB::table('hookcalls')
                ->where('hook_id', '=', $notification->id)
                ->whereRaw('created_at BETWEEN \''.$dt->format('Y-m-d').' 00:00:00\' AND \''.$dt->format('Y-m-d').' 23:59:59\'')
                ->count();

        if ($alreadySent) {
            return false;
        }

        return true;
    }

    public function wasCalled(Notification $notification)
    {
        DB::table('hookcalls')->insert([
            'hook_id'    => $notification->id,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    protected function buildDiscordEmbeds(): DiscordEmbedsMessage
    {
        $embeds = new DiscordEmbedsMessage();
        $embeds->setUsername('ESO Raidplanner');
        $embeds->setAvatar('https://esoraidplanner.com'.AbstractNotificationSystem::AVATAR_URL);
        $embeds->setTitle('Upcoming events for '.$this->subjects['guild']->name);
        $embeds->setColor(9660137);
        $embeds->setDescription($this->getText());
        $embeds->setAuthorName('ESO Raidplanner');
        $embeds->setAuthorIcon('https://esoraidplanner.com/favicon/appicon.jpg');
        $embeds->setAuthorUrl('https://esoraidplanner.com');
        $embeds->setFooterIcon('https://esoraidplanner.com/favicon/appicon.jpg');
        $embeds->setFooterText('ESO Raidplanner by Woeler');

        return $embeds;
    }

    protected function buildText(): string
    {
        if (0 === count($this->subjects['events'])) {
            return 'There are currently no upcoming events.';
        }

        $text = '';
        foreach ($this->notification->timezones() as $tz) {
            $zone     = new \DateTimeZone($tz);
            $zoneTime = new \DateTime('now', $zone);
            $text .= '**Timezone '.$tz.'** ('.$zoneTime->format('T').')'.PHP_EOL;
            foreach ($this->subjects['events'] as $event) {
                $dt = new \DateTime($event->start_date, new \DateTimeZone(env('DEFAULT_TIMEZONE')));
                $dt->setTimezone($zone);
                $text .= $event->name.' | _'.$dt->format('M jS H:i / g:i:a').'_'.PHP_EOL;
            }
            $text .= PHP_EOL;
        }

        return $text;
    }

    protected function hasNeededSubjects(): bool
    {
        return array_key_exists('events', $this->subjects) && array_key_exists('guild', $this->subjects);
    }
}
