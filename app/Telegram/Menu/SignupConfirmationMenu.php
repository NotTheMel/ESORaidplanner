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
use App\Utility\Classes;

class SignupConfirmationMenu extends Menu
{
    public static $menuId = 1127;

    /**
     * SignupConfirmationMenu constructor.
     */
    public function __construct()
    {
        $guildId = TelegramAPI::getGuildId();
        $eventId = TelegramAPI::getEventId();

        $event = EsoRaidPlannerAPI::getEvent(TelegramAPI::$username, $guildId, $eventId);

        if (null == $event) {
            $this->message[] = 'This event no longer exists.';
        } else {
            $message = 'Are you sure you want to signup for '.$event->name.' as:'.PHP_EOL;

            $preset = TelegramAPI::getPreset();

            if (!empty($preset->role)) {
                $message .= PHP_EOL.'Role: '.TelegramAPI::getShortRoleText($preset->role).PHP_EOL;
            } else {
                $message .= PHP_EOL.'Role: not specified'.PHP_EOL;
            }

            if (!empty($preset->class)) {
                $message .= 'Class: '.Classes::CLASSES[$preset->class].PHP_EOL;
            } else {
                $message .= 'Class: not specified'.PHP_EOL;
            }

            if (!empty($preset->supportSets)) {
                $message .= 'Support Sets: '.$preset->supportSets.PHP_EOL;
            }

            $comments = TelegramAPI::getComments();

            if (!empty($comments)) {
                $message .= PHP_EOL.'Comments: '.$comments;
            }

            $this->message[] = $message;

            $buttonData = ['event' => $event, 'preset' => $preset, 'comments' => $comments];

            $this->buttons[] = [new SignupYesButton('Yes', $buttonData), new SignupNoButton('No')];
        }
    }

    /**
     * @return SignupMenu
     */
    public function getParentMenu(): SignupMenu
    {
        return new SignupMenu();
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

class SignupYesButton extends Button
{
    /**
     * @param $sender
     *
     * @return SignupMenu
     */
    public function buttonClicked($sender): SignupMenu
    {
        $event    = $this->buttonData['event'];
        $preset   = $this->buttonData['preset'];
        $comments = $this->buttonData['comments'];

        EsoRaidPlannerAPI::Signup(TelegramAPI::$username, $event->guild_id, $event->id, $preset->role, $preset->class, $preset->supportSets, $comments);

        $menu = new SignupMenu();

        array_unshift($menu->message, 'You have been signed up for '.$event->name.'.');

        return $menu;
    }
}

class SignupNoButton extends Button
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
