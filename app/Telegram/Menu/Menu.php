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

use App\Telegram\Api\TelegramAPI;
use App\Telegram\Button\Button;

abstract class Menu
{
    public static $menuId;

    public $message;

    /* @var $buttons Button[][] */
    public $buttons;

    abstract public function getParentMenu();

    /**
     * @return array
     */
    public function getKeyboard(): array
    {
        $keyboard = [];

        foreach ($this->buttons as $index => $row) {
            $keyboard[$index] = [];

            foreach ($row as $button) {
                $keyboard[$index][] = $button->buttonText;
            }
        }

        return $keyboard;
    }

    /**
     * @return mixed
     */
    abstract public function messageSent();

    /**
     * @return SelectGuildMenu|mixed
     */
    public function buttonClicked()
    {
        switch (TelegramAPI::$message) {
            case '/start':
                $menu = new SelectGuildMenu();
                array_unshift($menu->message, 'Welcome to ESO Raid Planner Bot Beta.');

                return $menu;
        }

        foreach ($this->buttons as $row) {
            foreach ($row as $button) {
                if ($button->buttonText == TelegramAPI::$message) {
                    return $button->buttonClicked($this);
                }
            }
        }

        return $this->messageSent();
    }
}
