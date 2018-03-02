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

namespace App\Http\Controllers\Api;

use App\Singleton\TimeZones;
use Illuminate\Http\JsonResponse;

class TimeZoneController extends ApiController
{
    /**
     * Return list of supported timezones.
     *
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        return response(TimeZones::list(), 200);
    }
}
