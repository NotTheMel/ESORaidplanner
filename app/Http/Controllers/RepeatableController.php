<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 24.03.18
 * Time: 18:52.
 */

namespace App\Http\Controllers;

use App\Event;
use App\Guild;
use App\LogEntry;
use App\RepeatableEvent;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class RepeatableController extends Controller
{
    public function new(string $slug)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (!$guild->isAdmin(Auth::user())) {
            return redirect('g/'.$slug);
        }

        return view('repeatable_event.create', compact('guild'));
    }

    public function view(string $slug, string $repeatable_id)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (!$guild->isAdmin(Auth::user())) {
            return redirect('g/'.$slug);
        }

        $repeatable = RepeatableEvent::query()->find($repeatable_id);

        return view('repeatable_event.edit', compact('guild', 'repeatable'));
    }

    public function create(Request $request, string $slug)
    {
        $request->validate([
            'name'   => 'required',
            'year'   => 'required',
            'month'  => 'required',
            'day'    => 'required',
            'hour'   => 'required',
            'minute' => 'required',
            'interval' => 'required',
            'create_interval' => 'required',
        ]);

        if (12 === Auth::user()->clock) {
            $date = new DateTime(Input::get('year').'-'.Input::get('month').'-'.Input::get('day').' '.Input::get('hour').':'.Input::get('minute').' '.Input::get('meridiem'), new DateTimeZone(Auth::user()->timezone));
        } else {
            $date = new DateTime(Input::get('year').'-'.Input::get('month').'-'.Input::get('day').' '.Input::get('hour').':'.Input::get('minute'), new DateTimeZone(Auth::user()->timezone));
        }

        $date->setTimezone(new DateTimeZone(env('DEFAULT_TIMEZONE')));

        $guild = Guild::query()
            ->where('slug', '=', $slug)
            ->first();

        if (!$guild->isAdmin(Auth::user())) {
            return redirect('g/'.$slug);
        }

        $repeatable                  = new RepeatableEvent();
        $repeatable->name            = Input::get('name');
        $repeatable->type            = Input::get('type');
        $repeatable->latest_event    = $date->format('Y-m-d H:i:s');
        $repeatable->guild_id        = $guild->id;
        $repeatable->description     = Input::get('description') ?? '';
        $repeatable->tags            = Input::get('tags') ?? '';
        $repeatable->interval        = Input::get('interval');
        $repeatable->create_interval = ((int) Input::get('create_interval') * 86400);
        $repeatable->save();

        $event                    = new Event();
        $event->name              = Input::get('name');
        $event->type              = Input::get('type');
        $event->start_date        = $date->format('Y-m-d H:i:s');
        $event->guild_id          = $guild->id;
        $event->description       = Input::get('description') ?? '';
        $event->tags              = Input::get('tags') ?? '';
        $event->parent_repeatable = $repeatable->id;

        $event->save();

        $log = new LogEntry();
        $log->create($guild->id, Auth::user()->name.' created the event '.$event->name.'.');

        $event->callEventCreationHooks();

        return redirect('g/'.$slug.'/event/'.$event->id);
    }

    public function edit(Request $request, string $slug, int $repeatable_id)
    {
        $request->validate([
            'name' => 'required',
            'interval' => 'required',
            'create_interval' => 'required',
        ]);

        $repeatable                  = RepeatableEvent::query()->find($repeatable_id);
        $guild = Guild::query()->find($repeatable->guild_id);

        if (!$guild->isAdmin(Auth::user())) {
            return redirect('g/'.$slug);
        }

        $repeatable->name            = Input::get('name');
        $repeatable->type            = Input::get('type');
        $repeatable->description     = Input::get('description') ?? '';
        $repeatable->tags            = Input::get('tags') ?? '';
        $repeatable->interval        = Input::get('interval');
        $repeatable->create_interval = ((int) Input::get('create_interval') * 86400);
        $repeatable->save();

        return redirect('/g/'.$guild->slug.'/settings');
    }

    public function delete(string $slug, int $repeatable_id)
    {
        $repeatable = RepeatableEvent::query()->find($repeatable_id);
        $guild = Guild::query()->find($repeatable->guild_id);

        if (!$guild->isAdmin(Auth::user())) {
            return redirect('g/'.$slug);
        }

        Event::query()->where('parent_repeatable', '=', $repeatable->id)->update(['parent_repeatable' => null]);

        $repeatable->delete();

        return redirect('/g/'.$guild->slug.'/settings');
    }
}
