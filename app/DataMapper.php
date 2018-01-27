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

namespace App;

class DataMapper
{
    /**
     * @param int $role
     *
     * @return string
     */
    public static function getShortRoleText(int $role): string
    {
        switch ($role) {
            case 1:
                return 'Tank';

            case 2:
                return 'Healer';

            case 4:
                return 'Stamina DD';

            case 3:
                return 'Magicka DD';

            default:
                return '';
        }
    }

    /**
     * @param int $role
     *
     * @return string
     */
    public static function getRoleIcon(int $role): string
    {
        switch ($role) {
            case 1:
                return '🔰';

            case 2:
                return '⛑';

            case 4:
                return '⚔';

            case 3:
                return '🔮';

            default:
                return '';
        }
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public static function getMegaserverName(int $id): string
    {
        switch ($id) {
            case 1:
                return 'EU';

            case 2:
                return 'NA';

            default:
                return 'EU';
        }
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public static function getPlatformName(int $id): string
    {
        switch ($id) {
            case 1:
                return 'PC';

            case 2:
                return 'PS4';

            default:
                return 'XBOX';
        }
    }

    /**
     * @param string $class
     *
     * @return int
     */
    public static function getClassId(string $class): int
    {
        switch ($class) {
            case 'Dragonknight':
                return 1;

            case 'Sorcerer':
                return 2;

            case 'Nightblade':
                return 3;

            case 'Warden':
                return 4;

            case 'Templar':
                return 6;

            default:
                return '';
        }
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public static function getClassName(int $id): string
    {
        switch ($id) {
            case 1:
                return 'Dragonknight';

            case 2:
                return 'Sorcerer';

            case 3:
                return 'Nightblade';

            case 4:
                return 'Warden';

            case 6:
                return 'Templar';

            default:
                return '';
        }
    }

    /**
     * @param int $value
     *
     * @return string
     */
    public static function getPlatformShort(int $value): string
    {
        if (1 === $value) {
            return 'PC';
        } elseif (2 === $value) {
            return 'PS4';
        } elseif (3 === $value) {
            return 'XBOX';
        }

        return 'Unknown';
    }

    /**
     * @param int $value
     *
     * @return string
     */
    public static function getRoleName(int $value): string
    {
        if (1 === $value) {
            return 'Tank';
        } elseif (2 === $value) {
            return 'Healer';
        } elseif (3 === $value) {
            return 'Damage Dealer (Magicka)';
        } elseif (4 === $value) {
            return 'Damage Dealer (Stamina)';
        } elseif (5 === $value) {
            return 'Other';
        }

        return 'Unknown';
    }

    /**
     * @param string $role
     *
     * @return int
     */
    public static function getRoleId(string $role): int
    {
        switch ($role) {
            case 'Tank':
                return 1;

            case 'Healer':
                return 2;

            case 'Stamina DD':
                return 4;

            case 'Magicka DD':
                return 3;

            default:
                return 0;
        }
    }
}
