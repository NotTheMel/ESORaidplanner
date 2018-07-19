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

use App\Character;
use App\Event;
use App\Guild;
use App\Signup;
use App\Team;
use App\User;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = Event::query()
            ->where('start_date', '>=', date('Y-m-d H:i:s'))
            ->orderBy('start_date', 'asc')
            ->get();

        return view('event.events', compact('events'));
    }

    /**
     * @param string $slug
     * @param int    $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function detail(string $slug, int $id)
    {
        /** @var Event $event */
        $event = Event::query()->find($id);

        /** @var Guild $guild */
        $guild = Guild::query()
            ->where('slug', '=', $slug)
            ->first();

        return view('event.eventdetail', compact('event', 'guild'));
    }

    /**
     * @param string $slug
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function new(string $slug)
    {
        /** @var Guild $guild */
        $guild = Guild::query()
            ->where('slug', '=', $slug)
        ->first();

        return view('event.create', compact('guild'));
    }

    /**
     * @param string $slug
     * @param int    $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(string $slug, int $id)
    {
        $event = Event::query()->find($id);

        $guild = Guild::query()->where('slug', '=', $slug)->first();

        return view('event.edit', compact('event', 'guild'));
    }

    /**
     * @param Request $request
     * @param string  $slug
     * @param int     $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function signUpUser(Request $request, string $slug, int $id)
    {
        /** @var Event $event */
        $event = Event::query()->find($id);

        if (!empty($request->input('character'))) {
            $character = Character::query()->find($request->input('character'));
            $event->signup(Auth::user(), null, null, [], $character);
        } else {
            $event->signup(Auth::user(), $request->input('role'), $request->input('class'), $request->input('sets') ?? []);
        }

        return redirect('g/'.$slug.'/event/'.$id);
    }

    /**
     * @param Request $request
     * @param string  $slug
     * @param int     $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function signUpOther(Request $request, string $slug, int $id)
    {
        /** @var Event $event */
        $event = Event::query()->find($id);

        /** @var User $user */
        $user = User::query()->find($request->input('user'));

        if (!empty($request->input('character'))) {
            $character = Character::query()->find($request->input('character'));
            $event->signupOther($user, Auth::user(), null, null, [], $character);
        } else {
            $event->signupOther($user, Auth::user(), $request->input('role'), $request->input('class'), $request->input('sets') ?? []);
        }

        return redirect('g/'.$slug.'/event/'.$id);
    }

    /**
     * @param Request $request
     * @param string  $slug
     * @param int     $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function modifySignUp(Request $request, string $slug, int $id)
    {
        /** @var Event $event */
        $event = Event::query()->find($id);

        if (!empty($request->input('character'))) {
            $character = Character::query()->find($request->input('character'));
            $event->editSignup(Auth::user(), null, null, [], $character);
        } else {
            $event->editSignup(Auth::user(), $request->input('role'), $request->input('class'), $request->input('sets') ?? []);
        }

        return redirect('g/'.$slug.'/event/'.$id);
    }

    /**
     * @param string $slug
     * @param int    $event_id
     * @param int    $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function deleteSignup(string $slug, int $event_id, int $id)
    {
        /** @var Event $event */
        $event  = Event::query()->find($event_id);
        $signup = Signup::query()->find($id);
        $guild  = Guild::query()->find($event->guild_id);

        if (isset($signup->user_id)) {
            $event->signoffOther(User::query()->find($signup->user_id));
        }

        return redirect('/g/'.$slug.'/event/'.$event_id);
    }

    /**
     * @param string $slug
     * @param int    $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function signOffUser(string $slug, int $id)
    {
        /** @var Event $event */
        $event = Event::query()->find($id);

        $event->signoff(Auth::user());

        return redirect('g/'.$slug.'/event/'.$id);
    }

    /**
     * @param Request $request
     * @param string  $slug
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function create(Request $request, string $slug)
    {
        $request->validate([
            'name'   => 'required',
            'year'   => 'required',
            'month'  => 'required',
            'day'    => 'required',
            'hour'   => 'required',
            'minute' => 'required',
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

        $event              = new Event();
        $event->name        = $request->input('name');
        $event->type        = $request->input('type');
        $event->start_date  = $date->format('Y-m-d H:i:s');
        $event->guild_id    = $guild->id;
        $event->description = $request->input('description') ?? '';
        $event->tags        = $request->input('tags') ?? '';

        $event->save();

        if (!empty($request->input('team_id'))) {
            $team = Team::query()->find($request->input('team_id'));
            $event->signupTeam($team);
        }

        $event->logger->eventCreate($event, Auth::user());

        $event->callEventCreationHooks();

        return redirect('g/'.$slug.'/event/'.$event->id);
    }

    /**
     * @param Request $request
     * @param string  $slug
     * @param int     $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function edit(Request $request, string $slug, int $id)
    {
        $request->validate([
            'name'   => 'required',
            'year'   => 'required',
            'month'  => 'required',
            'day'    => 'required',
            'hour'   => 'required',
            'minute' => 'required',
        ]);

        if (12 === Auth::user()->clock) {
            $date = new DateTime($request->input('year').'-'.$request->input('month').'-'.$request->input('day').' '.$request->input('hour').':'.$request->input('minute').' '.$request->input('meridiem'), new DateTimeZone(Auth::user()->timezone));
        } else {
            $date = new DateTime($request->input('year').'-'.$request->input('month').'-'.$request->input('day').' '.$request->input('hour').':'.$request->input('minute'), new DateTimeZone(Auth::user()->timezone));
        }

        $date->setTimezone(new DateTimeZone(env('DEFAULT_TIMEZONE')));

        $event              = Event::query()->find($id);
        $event->name        = $request->input('name');
        $event->type        = $request->input('type');
        $event->start_date  = $date->format('Y-m-d H:i:s');
        $event->description = $request->input('description') ?? '';
        $event->tags        = $request->input('tags') ?? '';

        $event->save();

        return redirect('g/'.$slug.'/event/'.$id);
    }

    /**
     * @param string $slug
     * @param int    $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(string $slug, int $id)
    {
        $event = Event::query()->find($id);
        $event->logger->eventDelete($event, Auth::user());
        $event->delete();

        return redirect('g/'.$slug);
    }

    /**
     * @param Request $request
     * @param string  $slug
     * @param int     $event_id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function setSignupStatus(Request $request, string $slug, int $event_id)
    {
        if ('Confirm selected' === $request->post('action')) {
            $status = 1;
        } elseif ('Backup selected' === $request->post('action')) {
            $status = 2;
        } elseif ('Delete selected' === $request->post('action')) {
            $status = 'delete';
        } else {
            $status = 0;
        }

        $request->offsetUnset('action');

        $event = Event::query()->find($event_id);
        $guild = Guild::query()->find($event->guild_id);

        foreach ($request->all() as $signup) {
            if (is_numeric($signup) && is_numeric($status)) {
                $event->setSignupStatus($signup, $status);
            } elseif (is_numeric($signup) && 'delete' === $status) {
                Signup::query()->where('id', '=', $signup)->delete();
            }
        }

        return redirect('g/'.$guild->slug.'/event/'.$event->id);
    }

    /**
     * @param string $slug
     * @param int    $event_id
     * @param int    $lockstatus
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function changeLockStatus(string $slug, int $event_id, int $lockstatus)
    {
        $event = Event::query()->find($event_id);

        if (Event::STATUS_LOCKED === $lockstatus) {
            $event->lock();
        } else {
            $event->unlock();
        }

        return redirect('/g/'.$slug.'/event/'.$event_id);
    }

    /**
     * @param string $slug
     * @param int    $event_id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postSignupsHooks(string $slug, int $event_id)
    {
        $event         = Event::query()->find($event_id);
        $event->callPostSignupsHooks();

        return redirect('/g/'.$slug.'/event/'.$event_id);
    }
}
