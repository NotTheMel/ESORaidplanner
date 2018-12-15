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
use App\Utility\MegaServers;
use App\Utility\Platforms;

class SelectOperationMenu extends Menu
{
    public static $menuId = 11;

    /**
     * SelectOperationMenu constructor.
     *
     * @param array $message
     */
    public function __construct($message = [])
    {
        $guild = EsoRaidPlannerAPI::getGuild(TelegramAPI::$username, TelegramAPI::getGuildId());

        $this->message[] = 'Welcome to '.$guild->name.' - '.Platforms::PLATFORMS[$guild->platform].' '.MegaServers::MEGASERVERS[$guild->megaserver].PHP_EOL.PHP_EOL.'What would you like to do?';

        $this->buttons = [
            [new ShowEventsButton('Show Events')],
            [new SignupButton('Signup'), new SignoffButton('Signoff')],
            [new GoBackButton('Go Back')],
        ];
    }

    /**
     * @return SelectGuildMenu
     */
    public function getParentMenu(): SelectGuildMenu
    {
        return new SelectGuildMenu();
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

class ShowEventsButton extends Button
{
    /**
     * @param $sender
     *
     * @return ShowEventsMenu
     */
    public function buttonClicked($sender): ShowEventsMenu
    {
        return new ShowEventsMenu();
    }
}

class SignupButton extends Button
{
    /**
     * @param $sender
     *
     * @return SignupMenu
     */
    public function buttonClicked($sender): SignupMenu
    {
        return new SignupMenu();
    }
}

class SignoffButton extends Button
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
