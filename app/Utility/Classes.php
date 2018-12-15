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
 * Time: 16:34
 */

namespace App\Utility;

class Classes
{
    const DRAGONKNIGHT = 1;
    const SORCERER     = 2;
    const NIGHTBLADE   = 3;
    const WARDEN       = 4;
    const TEMPLAR      = 6;

    const CLASSES = [
        self::DRAGONKNIGHT => 'Dragonknight',
        self::SORCERER     => 'Sorcerer',
        self::NIGHTBLADE   => 'Nightblade',
        self::WARDEN       => 'Warden',
        self::TEMPLAR      => 'Templar',
    ];

    public static function getClassIcon(int $class_id): string
    {
        switch ($class_id) {
            case self::DRAGONKNIGHT:
                return 'dragon-knight-icon-eso.png';

            case self::SORCERER:
                return 'sorcerer-icon-eso.png';

            case self::NIGHTBLADE:
                return 'nightblade-icon-eso.png';

            case self::WARDEN:
                return 'warden_class_icon_morrowind-eso.png';

            case self::TEMPLAR:
                return 'templar-icon-eso.png';

            default:
                return '';
        }
    }
}
