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

class ClassTypes
{
    const CLASS_DRAGONKNIGHT = 1;
    const CLASS_SORCERER     = 2;
    const CLASS_NIGHTBLADE   = 3;
    const CLASS_WARDEN       = 4;
    const CLASS_TEMPLAR      = 6;

    /**
     * @param string $class_name
     *
     * @return int
     */
    public static function getClassId(string $class_name): int
    {
        switch ($class_name) {
            case 'Dragonknight':
                return self::CLASS_DRAGONKNIGHT;

            case 'Sorcerer':
                return self::CLASS_SORCERER;

            case 'Nightblade':
                return self::CLASS_NIGHTBLADE;

            case 'Warden':
                return self::CLASS_WARDEN;

            case 'Templar':
                return self::CLASS_TEMPLAR;

            default:
                return 0;
        }
    }

    /**
     * @param int $class_id
     *
     * @return string
     */
    public static function getClassName(int $class_id): string
    {
        switch ($class_id) {
            case self::CLASS_DRAGONKNIGHT:
                return 'Dragonknight';

            case self::CLASS_SORCERER:
                return 'Sorcerer';

            case self::CLASS_NIGHTBLADE:
                return 'Nightblade';

            case self::CLASS_WARDEN:
                return 'Warden';

            case self::CLASS_TEMPLAR:
                return 'Templar';

            default:
                return '';
        }
    }

    public static function getClassIcon(int $class_id): string
    {
        switch ($class_id) {
            case self::CLASS_DRAGONKNIGHT:
                return 'dragon-knight-icon-eso.png';

            case self::CLASS_SORCERER:
                return 'sorcerer-icon-eso.png';

            case self::CLASS_NIGHTBLADE:
                return 'nightblade-icon-eso.png';

            case self::CLASS_WARDEN:
                return 'warden_class_icon_morrowind-eso.png';

            case self::CLASS_TEMPLAR:
                return 'templar-icon-eso.png';

            default:
                return '';
        }
    }
}
