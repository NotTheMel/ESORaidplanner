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
 * Time: 17:13
 */

namespace App\Utility;

class Slugifier
{
    public static function slugify(string $string)
    {
        $string = strtolower(str_replace('\'', '', $string));

        return preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
    }
}
