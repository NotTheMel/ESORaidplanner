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
 * @see https://github.com/Woeler/eso-raid-planner
 */

namespace App\Http\Controllers;

use App\Event;
use App\Guild;
use App\Hook;
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

        $guild = new Guild();

        if (0 === $check) {
            $guild->name       = Input::get('name');
            $guild->slug       = Input::get('slug');
            $guild->megaserver = Input::get('megaserver');
            $guild->platform   = Input::get('platform');
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

        Hook::query()->where('guild_id', '=', $id)->delete();
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
        $count = DB::table('user_guilds')
            ->where('user_id', '=', Auth::id())
            ->where('guild_id', '=', $id)
            ->count();

        if ($count > 0) {
            return redirect('/');
        }

        DB::table('user_guilds')->insert([
            'user_id'  => Auth::id(),
            'guild_id' => $id,
            'status'   => 0,
        ]);

        $log = new LogEntry();
        $log->create($id, Auth::user()->name.' requested membership.');

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
        $guild = Guild::query()->where('id', '=', $guild_id)->first();

        if (!$guild->isAdmin(Auth::id())) {
            return redirect('/g/'.$guild->slug);
        }

        DB::table('user_guilds')->where('user_id', '=', $user_id)->where('guild_id', '=', $guild_id)->update(['status' => 1]);

        $user = User::query()->find($user_id);

        $log = new LogEntry();
        $log->create($guild->id, Auth::user()->name.' approved the membership request of '.$user->name.'.');

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
        $guild = Guild::query()->where('id', '=', $guild_id)->first();

        if (!$guild->isAdmin(Auth::id()) || $guild->owner_id === $user_id) {
            return redirect('/g/'.$guild->slug);
        }

        DB::table('user_guilds')->where('user_id', '=', $user_id)->where('guild_id', '=', $guild_id)->delete();

        $guild->removeAdmin($user_id);

        $user = User::query()->find($user_id);

        $log = new LogEntry();
        $log->create($guild->id, Auth::user()->name.' removed '.$user->name.' from the guild.');

        return redirect('/g/'.$guild->slug.'/members');
    }

    public function leaveGuild(string $slug)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if ($guild->isOwner(Auth::id())) {
            return redirect('/g/'.$slug);
        }

        $guild->removeAdmin(Auth::id());

        DB::table('user_guilds')->where('user_id', '=', Auth::id())->where('guild_id', '=', $guild->id)->delete();

        $log = new LogEntry();
        $log->create($guild->id, Auth::user()->name.' left the guild.');

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

        $guild->makeAdmin($user_id);

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

        $guild->removeAdmin($user_id);

        return redirect('/g/'.$slug.'/members');
    }

    /**
     * @param $slug
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail($slug)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (0 === $guild->userStatus()) {
            return view('guild.guild_awaiting_confirmation', compact('guild'));
        }

        if ($guild->isMember()) {
            $members = DB::table('user_guilds')->where('guild_id', '=', $guild->id)->where('status', '>=', 1)->get();

            $pending = DB::table('user_guilds')->where('guild_id', '=', $guild->id)->where('status', '=', 0)->count();

            $events = Event::query()->where('guild_id', '=', $guild->id)->
            where('start_date', '>=', date('Y-m-d H:i:s'))->orderBy('start_date', 'asc')->get();

            $count = Event::query()->where('guild_id', '=', $guild->id)->count();

            $logcount = LogEntry::query()->where('guild_id', '=', $guild->id)->count();

            return view('guild.guilddetail', compact('guild', 'members', 'events', 'pending', 'count', 'logcount'));
        }

        return view('guild.guild_apply', compact('guild'));
    }

    public function logs(string $slug)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (!$guild->isAdmin(Auth::id())) {
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

        if (!$guild->isAdmin(Auth::id())) {
            return redirect('/g/'.$slug);
        }

        $pending = DB::table('user_guilds')->where('guild_id', '=', $guild->id)->where('status', '=', 0)->get();

        return view('guild.settings', compact('guild', 'pending'));
    }

    public function deleteConfirm(int $id)
    {
        $guild = Guild::query()->find($id);

        if ($guild->owner_id !== Auth::id()) {
            return redirect('/g/'.$guild->slug);
        }

        return view('guild.delete_confirm', compact('guild'));
    }

    public function members(String $slug)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (!$guild->isMember()) {
            return redirect('/g/'.$slug);
        }

        $members = DB::table('user_guilds')
            ->join('users', 'user_guilds.user_id', 'users.id')
            ->where('user_guilds.guild_id', '=', $guild->id)
            ->where('user_guilds.status', '>=', 1)
            ->orderBy('users.name', 'asc')
            ->get();

        $pending = DB::table('user_guilds')
            ->join('users', 'user_guilds.user_id', 'users.id')
            ->where('user_guilds.guild_id', '=', $guild->id)
            ->where('user_guilds.status', '=', 0)
            ->orderBy('users.name', 'asc')
            ->get();

        return view('guild.members', compact('guild', 'members', 'pending'));
    }

    public function saveSettings(string $slug)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (!$guild->isAdmin(Auth::id())) {
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
}
