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
use App\Hook;
use App\Singleton\HookTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class HookController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function hookList()
    {
        $guild_ids = DB::table('user_guilds')->where('user_id', '=', Auth::id())->get();

        $guilds   = [];
        $guilds[] = 0;

        foreach ($guild_ids as $guild_id) {
            /** @var Guild $guild */
            $guild = Guild::query()->find($guild_id->guild_id);

            /** @var Guild $guild */
            if ($guild->isAdmin(Auth::id())) {
                $guilds[] = $guild->id;
            }
        }

        $hooks = Hook::query()->where('user_id', '=', Auth::id())->orWhereIn('guild_id', $guilds)->orderBy('name', 'asc')->get();

        return view('hook.hooks', compact('hooks'));
    }

    /**
     * @param int $type
     * @param int $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function modifyView(int $type, int $id)
    {
        if (!$this->isAdmin($id)) {
            return redirect('/hooks');
        }

        $hook = Hook::query()->find($id);

        return view('hook.hookdetail', compact('hook'));
    }

    /**
     * @param int $type
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createView(int $type)
    {
        $guilds_user = Auth::user()->getGuilds();

        $guilds    = [];
        $guilds[0] = 'Me as an individual';

        /** @var Guild $guild */
        foreach ($guilds_user as $key => $guild) {
            if ($guild->isAdmin(Auth::id())) {
                $guilds[$guild->id] = $guild->name;
            }
        }

        return view('hook.create', compact('type', 'guilds'));
    }

    /**
     * @param int $type
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function create(Request $request, int $type)
    {
        if (Hook::TYPE_DISCORD === $type) {
            $request->validate([
               'url' => new \App\Rules\DiscordHook(),
            ]);
        } elseif (Hook::TYPE_SLACK === $type) {
            $request->validate([
                'url' => new \App\Rules\SlackHook(),
            ]);
        }

        $hook      = new Hook();
        $hook->url = Input::get('url') ?? null;
        if (Hook::TYPE_TELEGRAM === $type) {
            $hook->token = env('TELEGRAM_TOKEN_NOTIFICATIONS');
        }
        $hook->chat_id   = Input::get('chat_id') ?? null;
        $hook->name      = Input::get('name');
        $hook->call_type = Input::get('call_type');
        if (HookTypes::ON_TIME === (int) $hook->call_type) {
            $hook->call_time_diff = (int) Input::get('call_time_diff') * 60;
        }
        $hook->if_less_signups = Input::get('if_less_signups') ?? null;
        $hook->type            = $type;
        $hook->active          = true;
        $hook->tags            = Input::get('tags') ?? null;
        $hook->message         = Input::get('message');

        if ('0' === Input::get('owner')) {
            $hook->user_id = Auth::id();
        } else {
            $hook->guild_id = Input::get('owner');
            /** @var Guild $guild */
            $guild            = Guild::query()->find($hook->guild_id);

            if (!$guild->isAdmin(Auth::id())) {
                return redirect('/hooks');
            }
        }

        $hook->save();

        return redirect('/hooks');
    }

    /**
     * @param int $type
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function modify(Request $request, int $type, int $id)
    {
        if (!$this->isAdmin($id)) {
            return redirect('/hooks');
        }

        if (Hook::TYPE_DISCORD === $type) {
            $request->validate([
                'url' => new \App\Rules\DiscordHook(),
            ]);
        } elseif (Hook::TYPE_SLACK === $type) {
            $request->validate([
                'url' => new \App\Rules\SlackHook(),
            ]);
        }

        $hook      = Hook::query()->find($id);
        $hook->url = Input::get('url') ?? null;
        if (Hook::TYPE_TELEGRAM === $type) {
            $hook->token = $hook->token ?? env('TELEGRAM_TOKEN_NOTIFICATIONS');
        }
        $hook->chat_id   = Input::get('chat_id') ?? null;
        $hook->name      = Input::get('name');
        if (HookTypes::ON_TIME === $hook->call_type) {
            $hook->call_time_diff = Input::get('call_time_diff') * 60;
        }
        $hook->if_less_signups = Input::get('if_less_signups') ?? null;
        $hook->type            = $type;
        $hook->active          = true;
        $hook->tags            = Input::get('tags') ?? null;

        $hook->save();

        return redirect('/hooks');
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(int $id)
    {
        /** @var Hook $hook */
        $hook = Hook::query()->find($id);

        if ($hook->isOwner(Auth::id())) {
            Hook::query()->where('id', '=', $id)->delete();
        }

        return redirect('/hooks');
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    private function isAdmin(int $id): bool
    {
        $hook = Hook::query()->where('id', '=', $id)->first();

        foreach (Auth::user()->getGuilds() as $guild) {
            if ($hook->guild_id === $guild->id && $guild->isAdmin(Auth::id())) {
                return true;
            }
        }

        return false;
    }
}
