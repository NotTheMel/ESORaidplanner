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

use App\Guild;
use App\Hook\NotificationHook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class HookController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function all()
    {
        $guild_ids = DB::table('user_guilds')->where('user_id', '=', Auth::id())->get();

        $guilds   = [];
        $guilds[] = 0;

        foreach ($guild_ids as $guild_id) {
            /** @var Guild $guild */
            $guild = Guild::query()->find($guild_id->guild_id);

            /** @var Guild $guild */
            if ($guild->isAdmin(Auth::user())) {
                $guilds[] = $guild->id;
            }
        }

        $hooks = NotificationHook::query()->where('user_id', '=', Auth::id())->orWhereIn('guild_id', $guilds)->orderBy('name', 'asc')->get();

        return view('hook.hooks', compact('hooks'));
    }

    /**
     * @param int $type
     * @param int $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function show(int $hook_id)
    {
        $hook = NotificationHook::query()->find($hook_id);

        return view('hook.edit', compact('hook'));
    }

    /**
     * @param int $type
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function new(int $call_type, int $type)
    {
        $guilds_user = Auth::user()->getGuilds();

        $guilds = [];

        /** @var Guild $guild */
        foreach ($guilds_user as $key => $guild) {
            if ($guild->isAdmin(Auth::user())) {
                $guilds[$guild->id] = $guild->name;
            }
        }

        return view('hook.create', compact('call_type', 'type', 'guilds'));
    }

    /**
     * @param Request $request
     * @param int     $call_type
     * @param int     $type
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function create(Request $request, int $call_type, int $type)
    {
        if (NotificationHook::TYPE_DISCORD === $type) {
            $request->validate([
                'url' => new \App\Rules\DiscordHook(),
            ]);
        } elseif (NotificationHook::TYPE_SLACK === $type) {
            $request->validate([
                'url' => new \App\Rules\SlackHook(),
            ]);
        }

        /** @var Guild $guild */
        $guild = Guild::query()->find(Input::get('guild_id'));
        if (!$guild->isAdmin(Auth::user())) {
            return redirect('/hooks');
        }

        $hook            = new NotificationHook($request->all());
        $hook->type      = $type;
        $hook->call_type = $call_type;
        $hook->active    = true;

        if (NotificationHook::TYPE_TELEGRAM === $type) {
            $hook->token = env('TELEGRAM_TOKEN_NOTIFICATIONS');
        }
        if (!empty($hook->call_time_diff)) {
            $hook->call_time_diff = 60 * $hook->call_time_diff;
        }

        $hook->save();

        return redirect('/hooks');
    }

    /**
     * @param Request $request
     * @param int     $hook_id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function edit(Request $request, int $hook_id)
    {
        $hook = NotificationHook::query()->find($hook_id);

        if (NotificationHook::TYPE_DISCORD === $hook->type) {
            $request->validate([
                'url' => new \App\Rules\DiscordHook(),
            ]);
        } elseif (NotificationHook::TYPE_SLACK === $hook->type) {
            $request->validate([
                'url' => new \App\Rules\SlackHook(),
            ]);
        }

        $all = $request->all();
        if (!empty($all['call_time_diff'])) {
            $all['call_time_diff'] = 60 * $all['call_time_diff'];
        }

        $hook->update($all);

        return redirect('/hooks');
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(int $id)
    {
        /** @var NotificationHook $hook */
        $hook = NotificationHook::query()->find($id);
        $hook->delete();

        return redirect('/hooks');
    }

    public function typeSelectForm(int $call_type)
    {
        return view('hook.type_select', compact('call_type'));
    }

    public function callTypeSelectForm()
    {
        return view('hook.calltype_select');
    }
}
