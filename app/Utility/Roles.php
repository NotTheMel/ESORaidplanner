<?php
/**
 * This file is part of the ESO-Database project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 *
 * @see https://eso-database.com
 * Created by woeler
 * Date: 12.09.18
 * Time: 10:42
 */

namespace App\Utility;

class Roles
{
    const TANK       = 1;
    const HEALER     = 2;
    const MAGICKA_DD = 3;
    const STAMINA_DD = 4;
    const OTHER      = 5;

    const ROLES = [
        self::TANK       => 'Tank',
        self::HEALER     => 'Healer',
        self::MAGICKA_DD => 'Magicka DD',
        self::STAMINA_DD => 'Stamina DD',
        self::OTHER      => 'Other',
    ];

    public static function getRoleIcon(int $role_id): string
    {
        switch ($role_id) {
            case self::TANK:
                return 'tank.png';
            case self::STAMINA_DD:
                return 'dd.png';
            case self::MAGICKA_DD:
                return 'dd.png';
            case self::HEALER:
                return 'healer.png';
            default:
                return '';
        }
    }

    public static function getRoleIconTelegram(int $role_id): string
    {
        switch ($role_id) {
            case self::TANK:
                return '🔰';

            case self::HEALER:
                return '⛑';

            case self::STAMINA_DD:
                return '⚔';

            case self::MAGICKA_DD:
                return '🔮';

            default:
                return '';
        }
    }
}
