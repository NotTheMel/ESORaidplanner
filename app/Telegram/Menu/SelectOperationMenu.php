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

use App\DataMapper;
use App\Telegram\Api\EsoRaidPlannerAPI;
use App\Telegram\Api\TelegramAPI;
use App\Telegram\Button\Button;
use App\Telegram\Button\GoBackButton;
use Illuminate\Support\Facades\Log;

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

        Log::info(print_r(TelegramAPI::$username, true));

        Log::info(print_r(TelegramAPI::getGuildId(), true));

        Log::info(print_r($guild, true));

        $this->message[] = 'Welcome to '.$guild->name.' - '.DataMapper::getPlatformName($guild->platform).' '.DataMapper::getMegaserverName($guild->megaserver).PHP_EOL.PHP_EOL.'What would you like to do?';

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
