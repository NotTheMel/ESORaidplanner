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

use App\Badge;
use App\Character;
use App\Comment;
use App\Event;
use App\Guild;
use App\Hook;
use App\LogEntry;
use App\Set;
use App\Signup;
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
        $event = Event::query()
            ->where('id', '=', $id)
            ->first();

        if (!$this->eventBelongsToGuild($slug, $id)) {
            return redirect('g/'.$slug);
        }

        $guild = Guild::query()
            ->where('slug', '=', $slug)
            ->first();

        $tanks = Signup::query()
            ->where('event_id', '=', $id)
            ->where('role_id', '=', 1)
            ->orderBy('created_at', 'asc')
            ->get();

        $healers = Signup::query()
            ->where('event_id', '=', $id)
            ->where('role_id', '=', 2)
            ->orderBy('created_at', 'asc')
            ->get();

        $magickas = Signup::query()
            ->where('event_id', '=', $id)
            ->where('role_id', '=', 3)
            ->orderBy('created_at', 'asc')
            ->get();

        $staminas = Signup::query()
            ->where('event_id', '=', $id)
            ->where('role_id', '=', 4)
            ->orderBy('created_at', 'asc')
            ->get();

        $others = Signup::query()
            ->where('event_id', '=', $id)
            ->where('role_id', '=', 5)
            ->orderBy('created_at', 'asc')
            ->get();

        $comments = Comment::query()
            ->where('event_id', '=', $event->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $sets_q = Set::query()
            ->orderBy('name', 'asc')
            ->get();

        $mem = DB::table('user_guilds')
            ->join('users', 'user_guilds.user_id', '=', 'users.id')
            ->where('user_guilds.guild_id', '=', $guild->id)
            ->where('user_guilds.status', '>=', 1)
            ->whereNotIn('user_id', $event->getSignUpIds())
            ->where('users.id', '<>', Auth::id())
            ->orderBy('users.name')->get();

        $members = [];

        foreach ($mem as $member) {
            $members[$member->user_id] = $member->name;
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
    public function createPage(string $slug)
    {
        $guild = Guild::query()
            ->where('slug', '=', $slug)
        ->first();

        if (!$guild->isAdmin(Auth::id())) {
            return redirect('g/'.$slug);
        }

        return view('event.create', compact('guild'));
    }

    public function editPage(string $slug, int $id)
    {
        $event = Event::query()->find($id);

        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (!$guild->isAdmin(Auth::id())) {
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
        $event = Event::query()->find($id);

        if (!$this->isGuildMember($slug) || !$this->eventBelongsToGuild($slug, $id) || 1 === $event->locked) {
            return redirect('g/'.$slug);
        }

        Signup::query()->where('event_id', '=', $id)->where('user_id', '=', Auth::id())->delete();

        $sign           = new Signup();
        $sign->user_id  = Auth::id();
        $sign->event_id = $id;

        if (!empty(Input::get('character'))) {
            if (0 == Input::get('character')) {
                return redirect('g/'.$slug.'/event/'.$id);
            }

            $character = Character::query()
                ->find(Input::get('character'));
            $sign->class_id     = $character->class;
            $sign->role_id      = $character->role;
            $sign->sets         = $character->sets;
            $sign->character_id = $character->id;
        } else {
            $sign->class_id = Input::get('class');
            $sign->role_id  = Input::get('role');

            if (!empty(Input::get('sets'))) {
                $sign->sets = implode(', ', Input::get('sets'));
            }
        }

        $sign->save();

        $log = new LogEntry();
        $log->create($this->getGuildId($slug), Auth::user()->name.' signed up for <a href="/g/'.$slug.'/event/'.$event->id.'">'.$event->name.'</a>.');

        if (1 == $sign->role_id) {
            Badge::earn(3);
        } elseif (2 == $sign->role_id) {
            Badge::earn(4);
        } elseif (3 == $sign->role_id) {
            Badge::earn(2);
        } elseif (4 == $sign->role_id) {
            Badge::earn(1);
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
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (!$guild->isAdmin(Auth::id()) || !$this->eventBelongsToGuild($slug, $id)) {
            return redirect('g/'.$slug);
        }

        Signup::query()->where('event_id', '=', $id)->where('user_id', '=', Input::get('user'))->delete();

        $sign = new Signup();

        $sign->event_id = $id;
        $sign->user_id  = Input::get('user');
        $sign->class_id = Input::get('class');
        $sign->role_id  = Input::get('role');
        if (!empty(Input::get('sets'))) {
            $sign->sets = implode(', ', Input::get('sets'));
        } else {
            $sign->sets = '';
        }

        $sign->save();

        $user  = User::query()->find(Input::get('user'));
        $event = Event::query()->find($id);

        $log = new LogEntry();
        $log->create($this->getGuildId($slug),
            Auth::user()->name.' signed up '.$user->name.' for <a href="/g/'.$slug.'/event/'.$event->id.'">'.$event->name.'</a>.');

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
        $sign = Signup::query()->where('event_id', '=', $id)
            ->where('user_id', '=', Auth::id())
            ->first();

        $event = Event::query()->find($sign->event_id);

        if (!$this->isGuildMember($slug) || !$this->eventBelongsToGuild($slug, $id) || 1 === $event->locked) {
            return redirect('g/'.$slug);
        }

        if (!empty(Input::get('character'))) {
            if (0 == Input::get('character')) {
                return redirect('g/'.$slug.'/event/'.$id);
            }

            $character = Character::query()
                ->find(Input::get('character'));
            $sign->class_id     = $character->class;
            $sign->role_id      = $character->role;
            $sign->sets         = $character->sets;
            $sign->character_id = $character->id;
        } else {
            $sign->class_id = Input::get('class');
            $sign->role_id  = Input::get('role');

            if (!empty(Input::get('sets'))) {
                $sign->sets = implode(', ', Input::get('sets'));
            } else {
                $sign->sets = '';
            }
            $sign->character_id = null;
        }

        $sign->save();

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
        $guild = Guild::query()
            ->where('slug', '=', $slug)
            ->first();

        $signup = Signup::query()->find($id);

        if ($guild->isAdmin(Auth::id())) {
            Signup::query()
                ->where('id', '=', $id)
                ->delete();

            $event = Event::query()->find($event_id);
            $user  = User::query()->find($signup->user_id);

            $log = new LogEntry();
            $log->create($this->getGuildId($slug), Auth::user()->name.' signed off '.$user->name.' for <a href="/g/'.$slug.'/event/'.$event->id.'">'.$event->name.'</a>.');
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
        $event = Event::query()->find($id);

        if (!$this->isGuildMember($slug) || !$this->eventBelongsToGuild($slug, $id) || 1 === $event->locked) {
            return redirect('g/'.$slug);
        }

        Signup::query()->where('event_id', '=', $id)->where('user_id', '=', Auth::id())->delete();

        $log = new LogEntry();
        $log->create($this->getGuildId($slug), Auth::user()->name.' signed off for <a href="/g/'.$slug.'/event/'.$event->id.'">'.$event->name.'</a>.');

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

        if (!$guild->isAdmin(Auth::id())) {
            return redirect('g/'.$slug);
        }

        $event              = new Event();
        $event->name        = Input::get('name');
        $event->type        = Input::get('type');
        $event->start_date  = $date->format('Y-m-d H:i:s');
        $event->guild_id    = $guild->id;
        $event->description = Input::get('description') ?? '';
        $event->tags = Input::get('tags') ?? '';

        $event->save();

        $log = new LogEntry();
        $log->create($guild->id, Auth::user()->name.' created the event '.$event->name.'.');

        $hooks = Hook::query()->where('call_type', '=', 1)->where('guild_id', '=', $guild->id)->get();

        foreach ($hooks as $hook) {
            $hook->call($event);
        }

        return redirect('g/'.$slug.'/event/'.$event->id);
    }

    /**
     * @param string $slug
     * @param int    $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function modifyEvent(string $slug, int $id)
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

        if (!$guild->isAdmin(Auth::id())) {
            return redirect('g/'.$slug);
        }

        $event              = Event::query()->find($id);
        $event->name        = Input::get('name');
        $event->type        = Input::get('type');
        $event->start_date  = $date->format('Y-m-d H:i:s');
        $event->description = Input::get('description') ?? '';
        $event->tags = Input::get('tags') ?? '';


        $event->save();

        return redirect('g/'.$slug.'/event/'.$id);
    }

    /**
     * @param string $slug
     * @param int    $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function deleteEvent(string $slug, int $id)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (!$guild->isAdmin(Auth::id())) {
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

        $event = Event::query()->find($signup->event_id);

        $guild = Guild::query()->find($event->guild_id);

        if (!$guild->isAdmin(Auth::id())) {
            return redirect('g/'.$guild->slug.'/event/'.$signup->event_id);
        }

        $signup->status = $status;

        $signup->save();

        return redirect('g/'.$guild->slug.'/event/'.$signup->event_id);
    }

    public function pastEvents(string $slug)
    {
        if (!$this->isGuildMember($slug)) {
            return redirect('g/'.$slug);
        }

        $guild = Guild::query()->where('slug', '=', $slug)->first();

        $events = Event::query()
            ->where('guild_id', '=', $guild->id)
            ->where('start_date', '<', date('Y-m-d H:i:s'))
            ->orderBy('start_date', 'desc')
            ->get();

        return view('event.events', compact('events', 'guild'));
    }

    public function changeLockStatus(string $slug, int $event_id, int $lockstatus)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (!$guild->isAdmin(Auth::id())) {
            return redirect('/g/'.$slug.'/event/'.$event_id);
        }

        $event         = Event::query()->find($event_id);
        $event->locked = $lockstatus;
        $event->save();

        return redirect('/g/'.$slug.'/event/'.$event_id);
    }

    private function eventBelongsToGuild(string $slug, int $event_id): bool
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        $event = Event::query()->find($event_id);

        if ($guild->id === $event->guild_id) {
            return true;
        }

        return false;
    }

    private function getGuildId(string $slug): int
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        return $guild->id;
    }
}
