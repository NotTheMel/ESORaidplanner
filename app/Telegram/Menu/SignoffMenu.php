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
 * @see https://github.com/Woeler/eso-raid-planner
 */

namespace App\Telegram\Menu;

use App\Telegram\Api\EsoRaidPlannerAPI;
use App\Telegram\Api\TelegramAPI;
use App\Telegram\Button\Button;
use App\Telegram\Button\GoBackButton;
use App\Telegram\Button\RefreshButton;
use DateTime;
use DateTimeZone;

class SignoffMenu extends Menu
{
    public static $menuId = 113;

    /**
     * SignoffMenu constructor.
     */
    public function __construct()
    {
        $guildId = TelegramAPI::getGuildId();

        $events = EsoRaidPlannerAPI::getSignedEvents(TelegramAPI::$username, $guildId);

        if (empty($events)) {
            $this->message[] = 'No events available for signoff.';
        } else {
            $this->message[] = 'From which event would you like to signoff?';

            foreach ($events as $event) {
                $profile = EsoRaidPlannerAPI::getProfile(TelegramAPI::$username);

                $startTime = new DateTime($event->start_date);
                $startTime->setTimezone(new DateTimeZone($profile->timezone));

                $buttonText      = $startTime->format('D, d M').' at '.$startTime->format('H:i').' - '.$event->name;
                $buttonData      = ['event' => $event];
                $this->buttons[] = [new SignoffItemButton($buttonText, $buttonData)];
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

class SignoffItemButton extends Button
{
    /**
     * @param $sender
     *
     * @return SignoffConfirmationMenu
     */
    public function buttonClicked($sender): SignoffConfirmationMenu
    {
        $event = $this->buttonData['event'];

        TelegramAPI::setEventId($event->id);

        return new SignoffConfirmationMenu();
    }
}
