<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 01.03.18
 * Time: 23:16.
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
    public function getTimezones(): JsonResponse
    {
        return response(TimeZones::list(), 200);
    }
}
