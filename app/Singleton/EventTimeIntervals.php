<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 24.03.18
 * Time: 18:42.
 */

namespace App\Singleton;

class EventTimeIntervals
{
    const INTERVALS = [
        604800  => 'Every week',
        1209600 => 'Every two weeks',
        1814400 => 'Every three weeks',
        2419200 => 'Every four weeks',
    ];
}
