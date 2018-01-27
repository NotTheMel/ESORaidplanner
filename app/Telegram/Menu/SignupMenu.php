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
use DateTime;
use DateTimeZone;

class SignupMenu extends Menu
{
    public static $menuId = 112;

    /**
     * SignupMenu constructor.
     */
    public function __construct()
    {
        $guildId = TelegramAPI::getGuildId();

        $events = EsoRaidPlannerAPI::getUnsignedEvents(TelegramAPI::$username, $guildId);

        if (empty($events)) {
            $this->message[] = 'No events available for signup.';
        } else {
            $this->message[] = 'For which event would you like to signup?';

            foreach ($events as $event) {
                $startTime = new DateTime($event->start_date);
                $startTime->setTimezone(new DateTimeZone(EsoRaidPlannerAPI::getProfile(TelegramAPI::$username)->timezone));

                $buttonText = $startTime->format('D, d M').' at '.$startTime->format('H:i').' - '.$event->name;
                $buttonData = ['event' => $event];

                $signupItemButton = new SignupItemButton($buttonText, $buttonData);

                $this->buttons[] = [$signupItemButton];
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

class SignupItemButton extends Button
{
    /**
     * @param $sender
     *
     * @return SelectPresetMenu
     */
    public function buttonClicked($sender): SelectPresetMenu
    {
        $event = $this->buttonData['event'];

        TelegramAPI::setEventId($event->id);

        return new SelectPresetMenu();
    }
}
