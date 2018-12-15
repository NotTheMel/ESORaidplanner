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
 * Time: 16:32
 */

namespace App\Utility;

class MegaServers
{
    const EU = 1;
    const NA = 2;

    const MEGASERVERS = [
        self::EU => 'EU',
        self::NA => 'NA',
    ];
}
