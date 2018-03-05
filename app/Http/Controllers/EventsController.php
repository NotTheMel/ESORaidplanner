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
use App\LogEntry;
use App\Set;
use App\Signup;
use App\Singleton\RoleTypes;
use App\User;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class EventsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$events = DB::table('events')->where('start_date', '>=', date('Y-m-d H:i:s'))->get();

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

        if (empty($guild) || empty($event) || $guild->id !== $event->guild_id) {
            return redirect('g/'.$slug);
        }

        $tanks = $event->getSignupsByRole(RoleTypes::ROLE_TANK);

        $healers = $event->getSignupsByRole(RoleTypes::ROLE_HEALER);

        $magickas = $event->getSignupsByRole(RoleTypes::ROLE_MAGICKA_DD);

        $staminas = $event->getSignupsByRole(RoleTypes::ROLE_STAMINA_DD);

        $others = $event->getSignupsByRole(RoleTypes::ROLE_OTHER);

        $comments = $event->getComments();

        $sets_q = Set::query()
            ->orderBy('name', 'asc')
            ->get();

        $mem = $guild->getMembers();

        $members = [];

        foreach ($mem as $member) {
            $members[$member->id] = $member->name;
        }

        $sets = [];

        foreach ($sets_q as $set) {
            $sets[$set->name] = $set->name;
        }

        return view('event.eventdetail', compact('tanks', 'event', 'guild', 'comments', 'sets', 'healers', 'staminas', 'magickas', 'others', 'members'));
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

        if (!$guild->isAdmin(Auth::user())) {
            return redirect('g/'.$slug);
        }

        return view('event.create', compact('guild'));
    }

    public function show(string $slug, int $id)
    {
        $event = Event::query()->find($id);

        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (!$guild->isAdmin(Auth::user())) {
            return redirect('g/'.$slug);
        }

        $start_date = new DateTime($event->start_date);

        $start_date->setTimezone(new DateTimeZone(Auth::user()->timezone));

        return view('event.edit', compact('event', 'start_date', 'guild'));
    }

    /**
     * @param string $slug
     * @param int    $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function signUpUser(string $slug, int $id)
    {
        /** @var Event $event */
        $event = Event::query()->find($id);

        if (!$this->isGuildMember($slug) || !$this->eventBelongsToGuild($slug, $id) || 1 === $event->locked) {
            return redirect('g/'.$slug);
        }

        if (!empty(Input::get('character'))) {
            $character = Character::query()->find(Input::get('character'));
            $event->signup(Auth::user(), null, null, [], $character);
        } else {
            $event->signup(Auth::user(), Input::get('role'), Input::get('class'), Input::get('sets') ?? []);
        }

        return redirect('g/'.$slug.'/event/'.$id);
    }

    /**
     * @param string $slug
     * @param int    $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function signUpOther(string $slug, int $id)
    {
        /** @var Event $event */
        $event = Event::query()->find($id);

        if (!$this->isGuildMember($slug) || !$this->eventBelongsToGuild($slug, $id) || 1 === $event->locked) {
            return redirect('g/'.$slug);
        }

        /** @var User $user */
        $user = User::query()->find(Input::get('user'));

        if (!empty(Input::get('character'))) {
            $character = Character::query()->find(Input::get('character'));
            $event->signupOther($user, Auth::user(), null, null, [], $character);
        } else {
            $event->signupOther($user, Auth::user(), Input::get('role'), Input::get('class'), Input::get('sets') ?? []);
        }

        return redirect('g/'.$slug.'/event/'.$id);
    }

    /**
     * @param string $slug
     * @param int    $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function modifySignUp(string $slug, int $id)
    {
        /** @var Event $event */
        $event = Event::query()->find($id);

        if (!$this->isGuildMember($slug) || !$this->eventBelongsToGuild($slug, $id) || 1 === $event->locked) {
            return redirect('g/'.$slug);
        }

        if (!empty(Input::get('character'))) {
            $character = Character::query()->find(Input::get('character'));
            $event->editSignup(Auth::user(), null, null, [], $character);
        } else {
            $event->editSignup(Auth::user(), Input::get('role'), Input::get('class'), Input::get('sets') ?? []);
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

        if ($guild->isAdmin(Auth::user())) {
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

        if (!$this->isGuildMember($slug) || !$this->eventBelongsToGuild($slug, $id) || 1 === $event->locked) {
            return redirect('g/'.$slug);
        }

        $event->signoff(Auth::user());

        return redirect('g/'.$slug.'/event/'.$id);
    }

    /**
     * @param string $slug
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function create(string $slug)
    {
        if (12 === Auth::user()->clock) {
            $date = new DateTime(Input::get('year').'-'.Input::get('month').'-'.Input::get('day').' '.Input::get('hour').':'.Input::get('minute').' '.Input::get('meridiem'), new DateTimeZone(Auth::user()->timezone));
        } else {
            $date = new DateTime(Input::get('year').'-'.Input::get('month').'-'.Input::get('day').' '.Input::get('hour').':'.Input::get('minute'), new DateTimeZone(Auth::user()->timezone));
        }

        $date->setTimezone(new DateTimeZone(date_default_timezone_get()));

        $guild = Guild::query()
            ->where('slug', '=', $slug)
            ->first();

        if (!$guild->isAdmin(Auth::user())) {
            return redirect('g/'.$slug);
        }

        $event              = new Event();
        $event->name        = Input::get('name');
        $event->type        = Input::get('type');
        $event->start_date  = $date->format('Y-m-d H:i:s');
        $event->guild_id    = $guild->id;
        $event->description = Input::get('description') ?? '';
        $event->tags        = Input::get('tags') ?? '';

        $event->save();

        $log = new LogEntry();
        $log->create($guild->id, Auth::user()->name.' created the event '.$event->name.'.');

        $event->callEventCreationHooks();

        return redirect('g/'.$slug.'/event/'.$event->id);
    }

    /**
     * @param string $slug
     * @param int    $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function edit(string $slug, int $id)
    {
        if (12 === Auth::user()->clock) {
            $date = new DateTime(Input::get('year').'-'.Input::get('month').'-'.Input::get('day').' '.Input::get('hour').':'.Input::get('minute').' '.Input::get('meridiem'), new DateTimeZone(Auth::user()->timezone));
        } else {
            $date = new DateTime(Input::get('year').'-'.Input::get('month').'-'.Input::get('day').' '.Input::get('hour').':'.Input::get('minute'), new DateTimeZone(Auth::user()->timezone));
        }

        $date->setTimezone(new DateTimeZone(date_default_timezone_get()));

        $guild = Guild::query()
            ->where('slug', '=', $slug)
            ->first();

        if (!$guild->isAdmin(Auth::user())) {
            return redirect('g/'.$slug);
        }

        $event              = Event::query()->find($id);
        $event->name        = Input::get('name');
        $event->type        = Input::get('type');
        $event->start_date  = $date->format('Y-m-d H:i:s');
        $event->description = Input::get('description') ?? '';
        $event->tags        = Input::get('tags') ?? '';

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
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (!$guild->isAdmin(Auth::user())) {
            return redirect('g/'.$slug);
        }

        $event = Event::query()->find($id);

        Signup::query()->where('event_id', '=', $id)->delete();
        Event::query()->where('id', '=', $id)->delete();

        $log = new LogEntry();
        $log->create($guild->id, Auth::user()->name.' deleted the event '.$event->name.'.');

        return redirect('g/'.$slug);
    }

    /**
     * @param string $slug
     *
     * @return bool
     */
    private function isGuildMember(string $slug): bool
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        $count = DB::table('user_guilds')->where('guild_id', '=', $guild->id)
            ->where('user_id', '=', Auth::id())
            ->where('status', '>=', 1)
            ->count();

        return $count > 0;
    }

    /**
     * @param int $signup_id
     * @param int $status
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function setSignupStatus(int $signup_id, int $status)
    {
        $signup = Signup::query()->find($signup_id);

        /** @var Event $event */
        $event = Event::query()->find($signup->event_id);

        $guild = Guild::query()->find($event->guild_id);

        if (!$guild->isAdmin(Auth::user())) {
            return redirect('g/'.$guild->slug.'/event/'.$signup->event_id);
        }

        $event->setSignupStatus($signup_id, $status);

        return redirect('g/'.$guild->slug.'/event/'.$signup->event_id);
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
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (!$guild->isAdmin(Auth::user())) {
            return redirect('/g/'.$slug.'/event/'.$event_id);
        }

        /** @var Event $event */
        $event         = Event::query()->find($event_id);

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
     * @return bool
     */
    private function eventBelongsToGuild(string $slug, int $event_id): bool
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        $event = Event::query()->find($event_id);

        if ($guild->id === $event->guild_id) {
            return true;
        }

        return false;
    }
}
