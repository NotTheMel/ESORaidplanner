<?php

namespace App;

use App\Singleton\EventTimeIntervals;
use Auth;
use DateTime;
use DateTimeZone;
use Illuminate\Database\Eloquent\Model;

/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 24.03.18
 * Time: 17:37.
 */
class RepeatableEvent extends Model
{
    protected $table = 'repeatable_events';

    public function getRepetitionString(): string
    {
        $dt = new DateTime($this->latest_event);
        $dt->setTimezone(new DateTimeZone(Auth::user()->timezone));

        return $dt->format('l').' '.EventTimeIntervals::INTERVALS[$this->interval];
    }
}
