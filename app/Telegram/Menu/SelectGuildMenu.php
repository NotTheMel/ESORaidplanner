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

use App\DataMapper;
use App\Telegram\Api\EsoRaidPlannerAPI;
use App\Telegram\Api\TelegramAPI;
use App\Telegram\Button\Button;
use App\Telegram\Button\RefreshButton;

class SelectGuildMenu extends Menu
{
    public static $menuId = 1;

    /**
     * SelectGuildMenu constructor.
     */
    public function __construct()
    {
        $guilds = EsoRaidPlannerAPI::getGuilds(TelegramAPI::$username);

        if (empty($guilds)) {
            $this->message[] = 'You are not a member of any of the available Guilds.'.PHP_EOL
                .'Please visit www.esoraidplanner.com to create or join a Guild.'.PHP_EOL.PHP_EOL
                .'For more information type /help.';
        } else {
            $this->message[] = PHP_EOL.'Please select a Guild.';

            foreach ($guilds as $guild) {
                $buttonText = DataMapper::getPlatformShort($guild->platform).' '.DataMapper::getMegaserverName($guild->megaserver).' - '.$guild->name;
                $buttonData = ['guild' => $guild];

                $guildItemButton = new GuildItemButton($buttonText, $buttonData);

                $this->buttons[] = [$guildItemButton];
            }
        }

        $this->buttons[] = [new RefreshButton('Refresh')];
    }

    /**
     * @return SelectGuildMenu
     */
    public function getParentMenu(): self
    {
        return new self();
    }

    /**
     * @return $this
     */
    public function messageSent()
    {
        array_unshift($this->message, 'You are not a member of this guild!');

        return $this;
    }
}

class GuildItemButton extends Button
{
    public function buttonClicked($sender)
    {
        $guild = $this->buttonData['guild'];

        TelegramAPI::setGuildId($guild->id);

        return new SelectOperationMenu();
    }
}
