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
use App\RepeatableEvent;
use App\Team;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RepeatableController extends Controller
{
    public function new(string $slug)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        return view('repeatable_event.create', compact('guild'));
    }

    public function view(string $slug, string $repeatable_id)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        $repeatable = RepeatableEvent::query()->find($repeatable_id);

        return view('repeatable_event.edit', compact('guild', 'repeatable'));
    }

    public function create(Request $request, string $slug)
    {
        $request->validate([
            'name'            => 'required',
            'year'            => 'required',
            'month'           => 'required',
            'day'             => 'required',
            'hour'            => 'required',
            'minute'          => 'required',
            'interval'        => 'required',
            'create_interval' => 'required',
        ]);

        if (12 === Auth::user()->clock) {
            $date = new DateTime($request->input('year').'-'.$request->input('month').'-'.$request->input('day').' '.$request->input('hour').':'.$request->input('minute').' '.$request->input('meridiem'), new DateTimeZone(Auth::user()->timezone));
        } else {
            $date = new DateTime($request->input('year').'-'.$request->input('month').'-'.$request->input('day').' '.$request->input('hour').':'.$request->input('minute'), new DateTimeZone(Auth::user()->timezone));
        }

        $date->setTimezone(new DateTimeZone(env('DEFAULT_TIMEZONE')));

        $guild = Guild::query()
            ->where('slug', '=', $slug)
            ->first();

        $repeatable                  = new RepeatableEvent();
        $repeatable->name            = $request->input('name');
        $repeatable->type            = $request->input('type');
        $repeatable->latest_event    = $date->format('Y-m-d H:i:s');
        $repeatable->guild_id        = $guild->id;
        $repeatable->description     = $request->input('description') ?? '';
        $repeatable->tags            = $request->input('tags') ?? '';
        $repeatable->interval        = $request->input('interval');
        $repeatable->create_interval = ((int) $request->input('create_interval') * 86400);
        $repeatable->default_team_id = $request->input('default_team_id') ?? null;
        $repeatable->save();

        $event                    = new Event();
        $event->name              = $request->input('name');
        $event->type              = $request->input('type');
        $event->start_date        = $date->format('Y-m-d H:i:s');
        $event->guild_id          = $guild->id;
        $event->description       = $request->input('description') ?? '';
        $event->tags              = $request->input('tags') ?? '';
        $event->parent_repeatable = $repeatable->id;

        if (!empty($repeatable->default_team_id)) {
            $team = Team::query()->find($repeatable->default_team_id);
            $event->signupTeam($team);
        }

        $event->save();
        $event->logger->eventCreate($event, Auth::user());
        $event->callEventCreationHooks();

        return redirect('g/'.$slug.'/event/'.$event->id);
    }

    public function edit(Request $request, string $slug, int $repeatable_id)
    {
        $request->validate([
            'name'            => 'required',
            'interval'        => 'required',
            'create_interval' => 'required',
        ]);

        $repeatable                  = RepeatableEvent::query()->find($repeatable_id);
        $guild                       = Guild::query()->find($repeatable->guild_id);

        $repeatable->name            = $request->input('name');
        $repeatable->type            = $request->input('type');
        $repeatable->description     = $request->input('description') ?? '';
        $repeatable->tags            = $request->input('tags') ?? '';
        $repeatable->interval        = $request->input('interval');
        $repeatable->create_interval = ((int) $request->input('create_interval') * 86400);
        $repeatable->default_team_id = $request->input('default_team_id') ?? null;
        $repeatable->save();

        $events = Event::query()->where('parent_repeatable', '=', $repeatable->id)
            ->where('start_date', '>', date('Y-m-d H:i:s'))
            ->get()->all();

        foreach ($events as $event) {
            $event->name        = $repeatable->name;
            $event->type        = $repeatable->type;
            $event->description = $repeatable->description;
            $event->tags        = $repeatable->tags;
            $event->save();
        }

        return redirect('/g/'.$guild->slug.'/settings');
    }

    public function delete(string $slug, int $repeatable_id)
    {
        $repeatable = RepeatableEvent::query()->find($repeatable_id);

        Event::query()->where('parent_repeatable', '=', $repeatable->id)->update(['parent_repeatable' => null]);

        $repeatable->delete();

        return redirect('/g/'.$slug.'/settings');
    }
}
