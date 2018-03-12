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

namespace App\Singleton;

class RoleTypes
{
    const ROLE_TANK       = 1;
    const ROLE_HEALER     = 2;
    const ROLE_MAGICKA_DD = 3;
    const ROLE_STAMINA_DD = 4;
    const ROLE_OTHER      = 5;

    /**
     * @param int $role_id
     *
     * @return string
     */
    public static function getRoleName(int $role_id): string
    {
        switch ($role_id) {
            case self::ROLE_TANK:
                return 'Tank';
            case self::ROLE_STAMINA_DD:
                return 'Damage Dealer (Stamina)';
            case self::ROLE_MAGICKA_DD:
                return 'Damage Dealer (Magicka)';
            case self::ROLE_HEALER:
                return 'Healer';
            default:
                return 'Other';
        }
    }

    /**
     * @param int $role_id
     *
     * @return string
     */
    public static function getShortRoleText(int $role_id): string
    {
        switch ($role_id) {
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
     * @param string $role_name
     *
     * @return int
     */
    public static function getRoleId(string $role_name): int
    {
        switch ($role_name) {
            case 'Tank':
                return self::ROLE_TANK;

            case 'Healer':
                return self::ROLE_HEALER;

            case 'Stamina DD':
                return self::ROLE_STAMINA_DD;

            case 'Magicka DD':
                return self::ROLE_MAGICKA_DD;

            default:
                return 0;
        }
    }

    /**
     * @param int $role_id
     *
     * @return string
     */
    public static function getRoleIcon(int $role_id): string
    {
        switch ($role_id) {
            case self::ROLE_TANK:
                return '🔰';

            case self::ROLE_HEALER:
                return '⛑';

            case self::ROLE_STAMINA_DD:
                return '⚔';

            case self::ROLE_MAGICKA_DD:
                return '🔮';

            default:
                return '';
        }
    }
}
