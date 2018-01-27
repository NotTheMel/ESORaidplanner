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

class SignoffConfirmationMenu extends Menu
{
    public static $menuId = 1131;

    /**
     * SignoffConfirmationMenu constructor.
     */
    public function __construct()
    {
        $guildId = TelegramAPI::getGuildId();
        $eventId = TelegramAPI::getEventId();

        $event = EsoRaidPlannerAPI::getEvent(TelegramAPI::$username, $guildId, $eventId);

        if (null == $event) {
            $this->message[] = 'Event doesn\'t exist!';
        } else {
            $this->message[] = 'Are you sure you want to signoff from '.$event->name.'?';
            $buttonData      = ['event' => $event];
            $this->buttons[] = [new SignoffYesButton('Yes', $buttonData), new SignoffNoButton('No')];
        }
    }

    /**
     * @return SignoffMenu
     */
    public function getParentMenu(): SignoffMenu
    {
        return new SignoffMenu();
    }

    /**
     * @return $this
     */
    public function messageSent()
    {
        array_unshift($this->message, 'Invalid command!');

        return $this;
    }
}

class SignoffYesButton extends Button
{
    /**
     * @param $sender
     *
     * @return SignoffMenu
     */
    public function buttonClicked($sender): SignoffMenu
    {
        $event = $this->buttonData['event'];

        EsoRaidPlannerAPI::Signoff(TelegramAPI::$username, $event->guild_id, $event->id);

        $menu = new SignoffMenu();

        array_unshift($menu->message, 'You have been signed off from '.$event->name.'.');

        return $menu;
    }
}

class SignoffNoButton extends Button
{
    /**
     * @param $sender
     *
     * @return SignoffMenu
     */
    public function buttonClicked($sender): SignoffMenu
    {
        return new SignoffMenu();
    }
}
