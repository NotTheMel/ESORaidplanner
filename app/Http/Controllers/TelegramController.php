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

namespace App\Http\Controllers;

use App\Telegram\Api\EsoRaidPlannerAPI;
use App\Telegram\Api\TelegramAPI;
use App\Telegram\Menu\AddCommentsMenu;
use App\Telegram\Menu\EnterSupportSetsMenu;
use App\Telegram\Menu\SavePresetMenu;
use App\Telegram\Menu\SelectClassMenu;
use App\Telegram\Menu\SelectGuildMenu;
use App\Telegram\Menu\SelectOperationMenu;
use App\Telegram\Menu\SelectPresetMenu;
use App\Telegram\Menu\SelectRoleMenu;
use App\Telegram\Menu\ShowEventsMenu;
use App\Telegram\Menu\SignoffConfirmationMenu;
use App\Telegram\Menu\SignoffMenu;
use App\Telegram\Menu\SignupConfirmationMenu;
use App\Telegram\Menu\SignupMenu;
use App\User;

class TelegramController extends Controller
{
    public function exec()
    {
        try {
            $updates = json_decode(file_get_contents('php://input'), true);

            TelegramAPI::load($updates);

            if (empty(TelegramAPI::$username)) {
                $message   = [];
                $message[] = 'You have not set a Telegram username in your Telegram settings. Please do so before using this bot.';
                TelegramAPI::reply($message, null);
                exit;
            }

            $user = User::query()->where('telegram_username', '=', TelegramAPI::$username)->count();

            if (1 !== $user) {
                $message   = [];
                $message[] = 'You have not configured your telegram username in the ESO Raidplanner. Please head over to https://esoraidplanner.com and configure it in your user settings. Your telegram username is: '.TelegramAPI::$username.'.';
                TelegramAPI::reply($message, null);
                exit;
            }

            $menuId = TelegramAPI::getMenuId();

            if (0 === $menuId || null === $menuId) {
                TelegramAPI::addUser();
            } elseif ($menuId !== SelectGuildMenu::$menuId) {
                $guild = EsoRaidPlannerAPI::getGuild(TelegramAPI::$username, TelegramAPI::getGuildId());
                if (empty($guild)) {
                    $menuId = 1;
                }
            }

            $menu = null;

            switch ($menuId) {
                case SelectGuildMenu::$menuId:
                    $menu = new SelectGuildMenu();
                    break;

                case SelectOperationMenu::$menuId:
                    $menu = new SelectOperationMenu();
                    break;

                case ShowEventsMenu::$menuId:
                    $menu = new ShowEventsMenu();
                    break;

                case SignupMenu::$menuId:
                    $menu = new SignupMenu();
                    break;

                case SelectPresetMenu::$menuId:
                    $menu = new SelectPresetMenu();
                    break;

                case SelectRoleMenu::$menuId:
                    $menu = new SelectRoleMenu();
                    break;

                case SelectClassMenu::$menuId:
                    $menu = new SelectClassMenu();
                    break;

                case EnterSupportSetsMenu::$menuId:
                    $menu = new EnterSupportSetsMenu();
                    break;

                case SavePresetMenu::$menuId:
                    $menu = new SavePresetMenu();
                    break;

                case AddCommentsMenu::$menuId:
                    $menu = new AddCommentsMenu();
                    break;

                case SignupConfirmationMenu::$menuId:
                    $menu = new SignupConfirmationMenu();
                    break;

                case SignoffMenu::$menuId:
                    $menu = new SignoffMenu();
                    break;

                case SignoffConfirmationMenu::$menuId:
                    $menu = new SignoffConfirmationMenu();
                    break;

                default:
                    $menu = new SelectGuildMenu();
                    break;
            }

            $menu = $menu->buttonClicked();

            TelegramAPI::setMenuId($menu::$menuId);

            TelegramAPI::reply($menu->message, $menu->getKeyboard());
        } catch (\Exception $e) {
            return;
        }
    }
}
