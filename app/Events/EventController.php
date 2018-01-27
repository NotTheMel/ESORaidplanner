<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 02.08.17
 * Time: 16:31.
 */

namespace App\Events;

use App\Http\Controllers\Controller;
use Woeler\EsoRaidPlanner\Repository\EventRepository;

class EventController extends Controller
{
    public function singleEvent($id)
    {
        $repo = new EventRepository();

        $event = $repo->get($id);

        return view('user.profile', ['event' => $event]);
    }
}
