<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 01.03.18
 * Time: 23:16.
 */

namespace App\Http\Controllers\Api;

use DateTime;
use DateTimeZone;
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
        static $regions = [
            DateTimeZone::AFRICA,
            DateTimeZone::AMERICA,
            DateTimeZone::ANTARCTICA,
            DateTimeZone::ASIA,
            DateTimeZone::ATLANTIC,
            DateTimeZone::AUSTRALIA,
            DateTimeZone::EUROPE,
            DateTimeZone::INDIAN,
            DateTimeZone::PACIFIC,
        ];

        $timezones = [];
        foreach ($regions as $region) {
            $timezones = array_merge($timezones, DateTimeZone::listIdentifiers($region));
        }

        $timezone_offsets = [];
        foreach ($timezones as $timezone) {
            $tz                          = new DateTimeZone($timezone);
            $timezone_offsets[$timezone] = $tz->getOffset(new DateTime());
        }

        // sort timezone by offset
        asort($timezone_offsets);

        $timezone_list = [];
        foreach ($timezone_offsets as $timezone => $offset) {
            $offset_prefix    = $offset < 0 ? '-' : '+';
            $offset_formatted = gmdate('H:i', abs($offset));

            $pretty_offset = "UTC${offset_prefix}${offset_formatted}";

            $timezone_list[$timezone] = "(${pretty_offset}) $timezone";
        }

        return response($timezone_list, 200);
    }
}
