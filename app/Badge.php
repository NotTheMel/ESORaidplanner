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

use DateTime;
use DateTimeZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Badge extends Model
{
    /**
     * @param int      $badge_id
     * @param int|null $user_id
     */
    public static function earn(int $badge_id, int $user_id = null)
    {
        $count = DB::table('user_badges')->where('badge_id', '=', $badge_id)->where('user_id', '=', $user_id ?? Auth::id())->count();

        if ($count > 0) {
            return;
        }

        DB::table('user_badges')->insert([
            'user_id'    => $user_id ?? Auth::id(),
            'badge_id'   => $badge_id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @return string
     */
    public function getNiceDate(): string
    {
        $date = new DateTime($this->start_date);

        $date->setTimezone(new DateTimeZone(Auth::user()->timezone));

        if (12 === Auth::user()->clock) {
            return $date->format('M jS Y g:i a');
        }

        return $date->format('M jS Y H:i');
    }
}
