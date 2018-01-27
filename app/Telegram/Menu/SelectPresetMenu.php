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
use App\Telegram\Button\RefreshButton;

class SelectPresetMenu extends Menu
{
    public static $menuId = 1121;

    /**
     * SelectPresetMenu constructor.
     */
    public function __construct()
    {
        $presets = EsoRaidPlannerAPI::getPresets(TelegramAPI::$username);

        if (empty($presets)) {
            $this->message = ['You don\'t have any character presets.'.PHP_EOL
                .'Would you like to add one?', ];
        } else {
            $this->message = ['Please select a character preset.'];

            foreach ($presets as $preset) {
                $presetButton = new PresetButton($preset->name, ['preset' => $preset]);

                $this->buttons[] = [$presetButton];
            }
        }

        $this->buttons[] = [new AddPresetButton('Add Preset')];
        $this->buttons[] = [new GoBackButton('Go Back'), new RefreshButton('Refresh')];
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
        array_unshift($this->message, 'Preset does not exist!');

        return $this;
    }
}

class SelectRoleMenu extends Menu
{
    public static $menuId = 1122;

    /**
     * SelectRoleMenu constructor.
     */
    public function __construct()
    {
        $this->message[] = 'Select your role.';

        $this->buttons = [
            [new RoleButton('Tank'), new RoleButton('Healer')],
            [new RoleButton('Stamina DD'), new RoleButton('Magicka DD')],
            [new GoBackButton('Go Back')],
        ];
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
        array_unshift($this->message, 'Invalid role!');

        return $this;
    }
}

class SelectClassMenu extends Menu
{
    public static $menuId = 1123;

    /**
     * SelectClassMenu constructor.
     */
    public function __construct()
    {
        $this->message[] = 'Select your class.';

        $this->buttons = [
            [new ClassButton('Dragonknight'), new ClassButton('Sorcerer')],
            [new ClassButton('Nightblade'), new ClassButton('Templar')],
            [new ClassButton('Warden')],
            [new GoBackButton('Go Back')],
        ];
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
        array_unshift($this->message, 'Invalid class');

        return $this;
    }
}

class EnterSupportSetsMenu extends Menu
{
    public static $menuId = 1124;

    /**
     * EnterSupportSetsMenu constructor.
     */
    public function __construct()
    {
        $this->message[] = 'Would you like to add some support sets?';

        $this->buttons = [[new GoBackButton('Go Back'), new SkipButton('Skip')]];
    }

    public function getParentMenu()
    {
        return new SignupMenu();
    }

    /**
     * @return SavePresetMenu
     */
    public function messageSent(): SavePresetMenu
    {
        $supportSets = '';

        if ('Skip' !== TelegramAPI::$message) {
            $supportSets = TelegramAPI::$message;
        }

        TelegramAPI::setSupportSets($supportSets);

        return new SavePresetMenu();
    }
}

class SavePresetMenu extends Menu
{
    public static $menuId = 1125;

    /**
     * SavePresetMenu constructor.
     */
    public function __construct()
    {
        $this->message = ['Please enter a name for this preset.'];

        $this->buttons = [[new GoBackButton('Go Back')]];
    }

    /**
     * @return SignupMenu
     */
    public function getParentMenu(): SignupMenu
    {
        return new SignupMenu();
    }

    /**
     * @return AddCommentsMenu
     */
    public function messageSent(): AddCommentsMenu
    {
        $preset = TelegramAPI::getPreset();

        $preset->name = TelegramAPI::$message;

        EsoRaidPlannerAPI::addPreset(TelegramAPI::$username, $preset->name, $preset->role, $preset->class, $preset->supportSets);

        return new AddCommentsMenu();
    }
}

class AddCommentsMenu extends Menu
{
    public static $menuId = 1126;

    /**
     * AddCommentsMenu constructor.
     */
    public function __construct()
    {
        $this->message[] = 'Do you have any comments?';

        $this->buttons = [[new GoBackButton('Go Back'), new SkipButton('Skip')]];
    }

    /**
     * @return SignupMenu
     */
    public function getParentMenu(): SignupMenu
    {
        return new SignupMenu();
    }

    /**
     * @return SignupConfirmationMenu
     */
    public function messageSent(): SignupConfirmationMenu
    {
        $comments = '';

        if ('Skip' !== TelegramAPI::$message) {
            $comments = TelegramAPI::$message;
        }

        TelegramAPI::setComments($comments);

        return new SignupConfirmationMenu();
    }
}

class PresetButton extends Button
{
    /**
     * @param $sender
     *
     * @return AddCommentsMenu
     */
    public function buttonClicked($sender): AddCommentsMenu
    {
        $preset = $this->buttonData['preset'];

        TelegramAPI::setRole($preset->role);
        TelegramAPI::setClass($preset->class);
        TelegramAPI::setSupportSets($preset->sets);

        return new AddCommentsMenu();
    }
}

class AddPresetButton extends Button
{
    /**
     * @param $sender
     *
     * @return SelectRoleMenu
     */
    public function buttonClicked($sender): SelectRoleMenu
    {
        return new SelectRoleMenu();
    }
}

class RoleButton extends Button
{
    /**
     * @param $sender
     *
     * @return SelectClassMenu
     */
    public function buttonClicked($sender): SelectClassMenu
    {
        $role = DataMapper::getRoleId($this->buttonText);

        TelegramAPI::setRole($role);

        return new SelectClassMenu();
    }
}

class ClassButton extends Button
{
    /**
     * @param $sender
     *
     * @return EnterSupportSetsMenu
     */
    public function buttonClicked($sender): EnterSupportSetsMenu
    {
        $class = $this->buttonText;

        TelegramAPI::setClass(DataMapper::getClassId($class));

        return new EnterSupportSetsMenu();
    }
}

class SkipButton extends Button
{
    /**
     * @param $sender
     *
     * @return mixed
     */
    public function buttonClicked($sender)
    {
        return $sender->messageSent();
    }
}
