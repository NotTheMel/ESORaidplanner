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

namespace App\Utility;

use Illuminate\Support\Facades\DB;

class Cron
{
    public static function start(string $signature)
    {
        if (self::exists($signature)) {
            DB::table('cron_status')
                ->where('signature', '=', $signature)
                ->update([
                    'status'   => 1,
                    'last_run' => date('Y-m-d H:i:d'),
                ]);
        } else {
            DB::table('cron_status')->insert([
                'signature' => $signature,
                'status'    => 1,
                'last_run'  => date('Y-m-d H:i:d'),
            ]);
        }
    }

    public static function finish(string $signature)
    {
        DB::table('cron_status')
            ->where('signature', '=', $signature)
            ->update([
                'status' => 0,
            ]);
    }

    public static function isRunning(string $signature): bool
    {
        $status = DB::table('cron_status')
            ->where('signature', '=', $signature)
            ->where('status', '=', 1)
            ->count();

        return 1 === $status;
    }

    private static function exists(string $signature)
    {
        return 1 === DB::table('cron_status')
            ->where('signature', '=', $signature)
            ->count();
    }
}
