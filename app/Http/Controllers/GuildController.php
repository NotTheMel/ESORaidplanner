<?php
/**
 * This file is part of the ESO-Database project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 *
 * @see https://eso-database.com
 * Created by woeler
 * Date: 12.09.18
 * Time: 17:03
 */

namespace App\Http\Controllers;

use App\Guild;
use App\User;
use App\Utility\Slugifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuildController extends Controller
{
    public function createView()
    {
        return view('guild.create');
    }

    public function detailView(string $slug)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        return view('guild.details', compact('guild'));
    }

    public function pastEventsView(string $slug)
    {
        /** @var Guild $guild */
        $guild  = Guild::query()->where('slug', '=', $slug)->first();
        $events = $guild->events()
            ->where('start_date', '<', date('Y-m-d H:i:s'))
            ->orderBy('start_date', 'desc')
            ->paginate(30);

        return view('guild.past_events', compact('guild', 'events'));
    }

    public function settingsView(string $slug)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        return view('guild.settings', compact('guild'));
    }

    public function deleteConfirmView(string $slug)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        return view('guild.delete_confirm', compact('guild'));
    }

    public function logsView(string $slug)
    {
        /** @var Guild $guild */
        $guild = Guild::query()->where('slug', '=', $slug)->first();
        $logs  = $guild->logs()->orderBy('created_at', 'desc')->paginate(30) ?? [];

        return view('guild.logs', compact('guild', 'logs'));
    }

    public function membersView(string $slug)
    {
        /** @var Guild $guild */
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        return view('guild.members', compact('guild'));
    }

    public function applyView(string $slug)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        return view('guild.apply', compact('guild'));
    }

    public function pendingView(string $slug)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        return view('guild.pending', compact('guild'));
    }

    public function listView()
    {
        $guilds = Guild::query()
            ->where('active', '=', 1)
            ->orderBy('name')->get()->all();

        return view('guild.list', compact('guilds'));
    }

    public function inactiveView(string $slug)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        return view('guild.deactivated', compact('guild'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'name'       => 'required',
            'megaserver' => 'required',
            'platform'   => 'required',
        ]);

        $guild           = new Guild($request->all());
        $guild->slug     = Slugifier::slugify($request->input('name'));
        $guild->owner_id = Auth::id();
        $guild->addAdmin(Auth::user());
        $guild->save();
        $guild->applyMember(Auth::user());
        $guild->approveMember(Auth::user());

        return redirect(route('guildDetailView', ['slug' => $guild->slug]));
    }

    public function delete(string $slug)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();
        $guild->delete();

        return redirect('/');
    }

    public function saveSettings(Request $request, string $slug)
    {
        /** @var Guild $guild */
        $guild                 = Guild::query()->where('slug', '=', $slug)->first();
        $guild->discord_widget = $request->input('discord_widget') ?? null;
        $guild->save();

        return redirect(route('guildSettingsView', ['slug' => $slug]));
    }

    public function apply(string $slug)
    {
        /** @var Guild $guild */
        $guild = Guild::query()->where('slug', '=', $slug)->first();
        $guild->applyMember(Auth::user());

        return redirect(route('guildDetailView', ['slug' => $slug]));
    }

    public function leave(string $slug)
    {
        /** @var Guild $guild */
        $guild = Guild::query()->where('slug', '=', $slug)->first();
        $guild->removeMember(Auth::user());

        return redirect('/');
    }

    public function approveMember(string $slug, int $user_id)
    {
        /** @var Guild $guild */
        $guild = Guild::query()->where('slug', '=', $slug)->first();
        $user  = User::query()->find($user_id);
        $guild->approveMember($user);

        return redirect(route('guildMembersView', ['slug' => $slug]));
    }

    public function removeMember(string $slug, int $user_id)
    {
        /** @var Guild $guild */
        $guild = Guild::query()->where('slug', '=', $slug)->first();
        $user  = User::query()->find($user_id);
        $guild->removeMember($user);

        return redirect(route('guildMembersView', ['slug' => $slug]));
    }

    public function addAdmin(string $slug, int $user_id)
    {
        /** @var Guild $guild */
        $guild = Guild::query()->where('slug', '=', $slug)->first();
        $user  = User::query()->find($user_id);
        $guild->addAdmin($user);

        return redirect(route('guildMembersView', ['slug' => $slug]));
    }

    public function removeAdmin(string $slug, int $user_id)
    {
        /** @var Guild $guild */
        $guild = Guild::query()->where('slug', '=', $slug)->first();
        $user  = User::query()->find($user_id);
        $guild->removeAdmin($user);

        return redirect(route('guildMembersView', ['slug' => $slug]));
    }

    public function makeOwner(string $slug, int $user_id)
    {
        /** @var Guild $guild */
        $guild = Guild::query()->where('slug', '=', $slug)->first();
        $user  = User::query()->find($user_id);
        $guild->transferOwnership($user);

        return redirect(route('guildDetailView', ['slug' => $slug]));
    }

    public function activate(string $slug)
    {
        $guild         = Guild::query()->where('slug', '=', $slug)->first();
        $guild->active = 1;
        $guild->save();

        return redirect(route('guildDetailView', ['slug' => $slug]));
    }

    public function disconnectDiscordBot(string $slug)
    {
        $guild                     = Guild::query()->where('slug', '=', $slug)->first();
        $guild->discord_id         = null;
        $guild->discord_channel_id = null;
        $guild->save();

        return redirect(route('guildSettingsView', ['slug' => $slug]));
    }
}
