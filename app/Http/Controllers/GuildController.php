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
use App\Hook\NotificationHook;
use App\LogEntry;
use App\Signup;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class GuildController extends Controller
{
    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function create(Request $request)
    {
        $request->validate([
            'name'       => 'required',
            'slug'       => new \App\Rules\GuildSlug(),
            'megaserver' => 'required',
            'platform'   => 'required',
        ]);

        $check = Guild::query()->where('slug', '=', Input::get('slug'))->count();

        if (0 === $check) {
            $guild             = new Guild($request->all());
            $guild->admins     = json_encode([Auth::id()]);
            $guild->owner_id   = Auth::id();
            $guild->image      = 'default.png';

            $guild->save();

            DB::table('user_guilds')->insert([
                'user_id'  => Auth::id(),
                'guild_id' => $guild->id,
                'status'   => 1,
            ]);

            $log = new LogEntry();
            $log->create($guild->id, Auth::user()->name.' created the guild '.$guild->name.'.');

            return redirect('g/'.$guild->slug);
        }

        return redirect('guild/create');
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(int $id)
    {
        $guild = Guild::query()->where('id', '=', $id)->first();

        if ($guild->owner_id !== Auth::id()) {
            return redirect('/g/'.$guild->slug);
        }

        NotificationHook::query()->where('guild_id', '=', $id)->delete();
        $events = Event::query()->where('guild_id', '=', $id)->get();

        foreach ($events as $event) {
            Signup::query()->where('event_id', '=', $event->id)->delete();
        }

        Event::query()->where('guild_id', '=', $id)->delete();

        DB::table('user_guilds')->where('guild_id', '=', $id)->delete();

        Guild::query()->where('id', '=', $id)->delete();

        LogEntry::query()->where('guild_id', '=', $id)->delete();

        return redirect('/');
    }

    /**
     * @param string $slug
     * @param int    $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function requestMembership(string $slug, int $id)
    {
        $guild = Guild::query()->find($id);

        $guild->requestMembership(Auth::user());

        return redirect('/');
    }

    /**
     * @param string $slug
     * @param int    $guild_id
     * @param int    $user_id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function approveMembership(string $slug, int $guild_id, int $user_id)
    {
        $guild = Guild::query()->find($guild_id);

        if (!$guild->isAdmin(Auth::user())) {
            return redirect('/g/'.$guild->slug);
        }

        $user = User::query()->find($user_id);

        $guild->approveMembership($user);

        return redirect('/g/'.$guild->slug.'/members');
    }

    /**
     * @param string $slug
     * @param int    $guild_id
     * @param int    $user_id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function removeMembership(string $slug, int $guild_id, int $user_id)
    {
        /** @var Guild $guild */
        $guild = Guild::query()->find($guild_id);
        /** @var User $user */
        $user = User::query()->find($user_id);

        if (!$guild->isAdmin(Auth::user())) {
            return redirect('/g/'.$guild->slug);
        }
        if (!$guild->isOwner(Auth::user()) && $guild->isAdmin($user)) {
            return redirect('/g/'.$guild->slug);
        }

        $guild->removeMembership($user);

        return redirect('/g/'.$guild->slug.'/members');
    }

    public function leave(string $slug)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        $guild->leave(Auth::user());

        return redirect('/');
    }

    /**
     * @param string $slug
     * @param int    $user_id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function makeAdmin(string $slug, int $user_id)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if ($guild->owner_id === Auth::id()) {
            $guild->makeAdmin(User::query()->find($user_id));
        }

        return redirect('/g/'.$slug.'/members');
    }

    /**
     * @param string $slug
     * @param int    $user_id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function removeAdmin(string $slug, int $user_id)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if ($guild->owner_id === Auth::id()) {
            $guild->removeAdmin(User::query()->find($user_id));
        }

        return redirect('/g/'.$slug.'/members');
    }

    /**
     * @param $slug
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail($slug)
    {
        /** @var Guild $guild */
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (0 === $guild->userStatus(Auth::user())) {
            return view('guild.guild_awaiting_confirmation', compact('guild'));
        }

        if ($guild->isMember(Auth::user())) {
            $members = $guild->getMembers();

            $pending = $guild->getPendingMembers();

            $events = $guild->getEvents();

            $count = Event::query()->where('guild_id', '=', $guild->id)->count();

            $logcount = LogEntry::query()->where('guild_id', '=', $guild->id)->count();

            return view('guild.guilddetail', compact('guild', 'members', 'events', 'pending', 'count', 'logcount'));
        }

        return view('guild.guild_apply', compact('guild'));
    }

    public function logs(string $slug)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (!$guild->isAdmin(Auth::user())) {
            return redirect('/g/'.$guild->slug);
        }

        $logs = LogEntry::query()->where('guild_id', '=', $guild->id)->orderBy('created_at', 'desc')->paginate(50);

        return view('guild.logs', compact('guild', 'logs'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function listAll()
    {
        $guilds = Guild::query()->orderBy('name', 'asc')->get();

        return view('guilds', compact('guilds'));
    }

    public function settings(string $slug)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (!$guild->isAdmin(Auth::user())) {
            return redirect('/g/'.$slug);
        }

        $pending = $guild->getPendingMembers();

        return view('guild.settings', compact('guild', 'pending'));
    }

    public function deleteConfirm(int $id)
    {
        $guild = Guild::query()->find($id);

        if (!$guild->isOwner(Auth::user())) {
            return redirect('/g/'.$guild->slug);
        }

        return view('guild.delete_confirm', compact('guild'));
    }

    public function members(String $slug)
    {
        /** @var Guild $guild */
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (!$guild->isMember(Auth::user())) {
            return redirect('/g/'.$slug);
        }

        $members = $guild->getMembers();

        $pending = $guild->getPendingMembers();

        return view('guild.members', compact('guild', 'members', 'pending'));
    }

    public function saveSettings(string $slug)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (!$guild->isAdmin(Auth::user())) {
            return redirect('/g/'.$slug);
        }

        $guild->discord_widget = Input::get('discord_widget') ?? null;

        if (null !== $guild->discord_widget) {
            $guild->discord_widget = preg_replace(
                ['/width="\d+"/i'],
                [sprintf('width="%d"', '100')],
                $guild->discord_widget);
            $guild->discord_widget = str_replace('"100"', '"100%"', $guild->discord_widget);
        }

        $guild->save();

        return redirect('/g/'.$slug.'/settings');
    }

    /**
     * @param string $slug
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function pastEvents(string $slug)
    {
        /** @var Guild $guild */
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        $events = $guild->getPastEvents();

        return view('event.events', compact('events', 'guild'));
    }
}
