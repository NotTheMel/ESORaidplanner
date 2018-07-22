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

use Illuminate\Database\Eloquent\Model;

class Set extends Model
{
    protected $fillable = [
        'name',
        'version',
    ];

    public static function Array($sets): array
    {
        if (empty($sets)) {
            return [];
        }
        if (\is_array($sets)) {
            return $sets;
        }
        if (false !== strpos($sets, ', ')) {
            return explode(', ', $sets);
        }

        return [$sets];
    }

    public static function formatForDropdown(): array
    {
        $sets      = self::all() ?? [];
        $formatted = [];

        foreach ($sets as $set) {
            $formatted[$set->name] = $set->name;
        }

        return $formatted;
    }
}
