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

namespace App\Telegram\Menu;

use App\Telegram\Api\EsoRaidPlannerAPI;
use App\Telegram\Api\TelegramAPI;
use App\Telegram\Button\Button;
use App\Telegram\Button\GoBackButton;
use App\Telegram\Button\RefreshButton;
use App\Utility\Classes;
use App\Utility\Roles;
use DateTime;
use DateTimeZone;

class ShowEventsMenu extends Menu
{
    public static $menuId = 111;

    /**
     * ShowEventsMenu constructor.
     */
    public function __construct()
    {
        $guildId = TelegramAPI::getGuildId();

        $events = EsoRaidPlannerAPI::getEvents(TelegramAPI::$username, $guildId);

        if (empty($events)) {
            $this->message[] = 'No events available.';
        } else {
            $this->message[] = 'Which event would you like to show?';

            foreach ($events as $event) {
                $profile = EsoRaidPlannerAPI::getProfile(TelegramAPI::$username);

                $startTime = new DateTime($event->start_date);
                $startTime->setTimezone(new DateTimeZone($profile->timezone));

                $buttonText = $startTime->format('D, d M').' at '.$startTime->format('H:i').' - '.$event->name;
                $buttonData = ['event' => $event];

                $this->buttons[] = [new EventItemButton($buttonText, $buttonData)];
            }
        }

        $this->buttons[] = [new GoBackButton('Go Back'), new RefreshButton('Refresh')];
    }

    /**
     * @return SelectOperationMenu
     */
    public function getParentMenu(): SelectOperationMenu
    {
        return new SelectOperationMenu();
    }

    /**
     * @return $this
     */
    public function messageSent()
    {
        array_unshift($this->message, 'This event does not exist!');

        return $this;
    }
}

class EventItemButton extends Button
{
    /**
     * @param $sender
     *
     * @return mixed
     */
    public function buttonClicked($sender)
    {
        $event = $this->buttonData['event'];

        $message = '';

        if (!empty($event->description)) {
            $message = $event->description.PHP_EOL;
        }

        $signups = EsoRaidPlannerAPI::getEventSignups(TelegramAPI::$username, $event->guild_id, $event->id);

        if (empty($signups)) {
            $message .= PHP_EOL.'No signups yet.';
        } else {
//                // Sorting signups by name
//                usort($signups, function($lhs, $rhs)
//                {
//                    return strcmp($lhs->username, $rhs->username);
//                });
//
//                // Sorting signups by role //TODO : Fix order
//                usort($signups, function($lhs, $rhs)
//                {
//                    return strcmp($rhs->role, $lhs->role);
//                });

//                if(!is_array($signups)){
//                    $signups = [$signups];
//                }

            $signupsFormatted  = '';
            $supportFormatted  = '';
            $commentsFormatted = '';

            foreach ($signups as $key => $signup) {
                $profile = EsoRaidPlannerAPI::getProfileById($signup->user_id);
                $icon    = Roles::getRoleIconTelegram($signup->role_id);

                $signupsFormatted .= ($key + 1).') '.$icon.' '.$profile->name.' - '.Classes::CLASSES[$signup->class_id].PHP_EOL;

                if (!empty($signup->sets)) {
                    $supportFormatted .= ' - '.$profile->name.': '.$signup->sets.PHP_EOL;
                }

                if (!empty($signup->comments)) {
                    $commentsFormatted .= ' - '.$profile->name.': '.$signup->comments.PHP_EOL;
                }
            }

            $message .= PHP_EOL.'Signups ('.count($signups).'):'.PHP_EOL.$signupsFormatted;

            if (!empty($supportFormatted)) {
                $message .= PHP_EOL.'Support:'.PHP_EOL.$supportFormatted;
            }

            if (!empty($commentsFormatted)) {
                $message .= PHP_EOL.'Comments:'.PHP_EOL.$commentsFormatted;
            }
        }

        $sender->message = [$message];

        return $sender;
    }
}
