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

namespace App\Notification\System;

use App\Notification\Notification;
use Woeler\DiscordPhp\Message\DiscordTextMessage;
use Woeler\DiscordPhp\Webhook\DiscordWebhook;

class DiscordSystem extends AbstractNotificationSystem
{
    const SYSTEM_ID = 1;
    const NAME      = 'Discord';

    public function send(Notification $notification)
    {
        if (empty($this->embeds) || !$notification->hasEmbeds()) {
            $m = new DiscordTextMessage();
            $m->setUsername('ESO Raidplanner');
            $m->setAvatar('https://esoraidplanner.com'.AbstractNotificationSystem::AVATAR_URL);
            $m->setContent($this->messageObj->getText());
            $hook = new DiscordWebhook($notification->url, $m);
            $hook->send();
        } else {
            $hook = new DiscordWebhook($notification->url, $this->embeds);
            $hook->send();
        }
    }
}
