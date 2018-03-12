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
}
