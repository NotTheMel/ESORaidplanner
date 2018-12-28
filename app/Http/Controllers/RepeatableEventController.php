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

namespace App\Http\Controllers;

use App\Event;
use App\Guild;
use App\RepeatableEvent;
use App\Utility\TagHandler;
use App\Utility\UserDateHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RepeatableEventController extends Controller
{
    public function createView(string $slug)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        return view('event.recurring.create', compact('guild'));
    }

    public function updateView(string $slug, int $repeatable_id)
    {
        $guild      = Guild::query()->where('slug', '=', $slug)->first();
        $repeatable = RepeatableEvent::query()->find($repeatable_id);

        return view('event.recurring.update', compact('guild', 'repeatable'));
    }

    public function create(Request $request, string $slug)
    {
        $request->validate([
            'name'             => 'required',
            'year'             => 'required',
            'month'            => 'required',
            'day'              => 'required',
            'hour'             => 'required',
            'minute'           => 'required',
            'interval'         => 'required',
            'max_create_ahead' => 'required',
            'timezone'         => 'required',
        ]);

        $guild      = Guild::query()->where('slug', '=', $slug)->first();
        $first_date = UserDateHandler::requestToDateTime($request->all(), Auth::user()->clock, new \DateTimeZone($request->input('timezone')));

        $event              = new Event();
        $event->name        = $request->input('name');
        $event->description = $request->input('description');
        $event->guild_id    = $guild->id;
        $event->start_date  = $first_date->format('Y-m-d H:i:s');
        $event->tags        = TagHandler::stringToArray($request->input('tags') ?? '');
        $event->save();

        $repeatable                    = new RepeatableEvent($request->except('tags'));
        $repeatable->latest_event      = $event->id;
        $repeatable->latest_event_date = $event->start_date;
        $repeatable->start_date        = $first_date->format('Y-m-d H:i:s');
        $repeatable->tags              = TagHandler::stringToArray($request->input('tags') ?? '');
        $repeatable->guild_id          = $guild->id;
        $repeatable->max_create_ahead  = $request->input('max_create_ahead') ?? 1;
        $repeatable->save();

        $event->parent_repeatable = $repeatable->id;
        $event->save();

        return redirect(route('guildSettingsView', ['slug' => $slug]));
    }

    public function update(string $slug, int $repeatable_id)
    {
    }

    public function delete(string $slug, int $repeatable_id)
    {
        $repeatable = RepeatableEvent::query()->find($repeatable_id);
        $repeatable->delete();

        return redirect(route('guildSettingsView', ['slug' => $slug]));
    }
}
