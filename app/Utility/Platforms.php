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
 * Time: 16:31
 */

namespace App\Utility;

class Platforms
{
    const PC   = 1;
    const PS4  = 2;
    const XBOX = 3;

    const PLATFORMS = [
        self::PC   => 'PC/Mac',
        self::PS4  => 'PlayStation 4',
        self::XBOX => 'XBOX One',
    ];
}
