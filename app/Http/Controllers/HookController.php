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
            $guild = Guild::query()->where('id', '=', $guild_id->guild_id)->first();

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

        $hook = Hook::query()->where('id', '=', $id)->limit(1)->get();

        $hook = $hook->first();

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
        $data = [];

        if (1 === $type) {
            $request->validate([
               'url' => new \App\Rules\DiscordHook(),
            ]);
        } elseif (3 === $type) {
            $request->validate([
                'url' => new \App\Rules\SlackHook(),
            ]);
        }

        if (1 === $type || 3 === $type) {
            $data['url'] = Input::get('url');
        } elseif (2 === $type) {
            $data['token']   = env('TELEGRAM_TOKEN_NOTIFICATIONS');
            $data['chat_id'] = Input::get('chat_id');
        } else {
            redirect('/hooks');
        }

        $data['name'] = Input::get('name');
        if (2 == Input::get('call_type')) {
            $data['call_time_diff'] = Input::get('call_time_diff') * 60;
        }
        if (!empty(Input::get('if_less_signups')) && 0 !== Input::get('if_less_signups')) {
            $data['if_less_signups'] = Input::get('if_less_signups');
        }
        $data['call_type']  = Input::get('call_type');
        $data['message']    = Input::get('message');
        $data['type']       = $type;
        $data['active']     = true;
        $data['created_at'] = date('Y-m-d H:i:s');

        if ('0' === Input::get('owner')) {
            $data['user_id'] = Auth::id();
        } else {
            $data['guild_id'] = Input::get('owner');
            $guild            = Guild::query()->where('id', '=', $data['guild_id'])->first();

            if (!$guild->isAdmin(Auth::id())) {
                return redirect('/hooks');
            }
        }

        Hook::query()->insert($data);

        return redirect('/hooks');
    }

    /**
     * @param int $type
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function modify(int $type, int $id)
    {
        if (!$this->isAdmin($id)) {
            return redirect('/hooks');
        }

        $data = [];

        if (1 === $type || 3 === $type) {
            $data['url'] = Input::get('url');
        } elseif (2 === $type) {
            $data['chat_id'] = Input::get('chat_id');
        } else {
            redirect('/hooks');
        }

        if (!empty(Input::get('if_less_signups')) && 0 !== Input::get('if_less_signups')) {
            $data['if_less_signups'] = Input::get('if_less_signups');
        } else {
            $data['if_less_signups'] = 0;
        }

        $data['name'] = Input::get('name');
        if (!empty(Input::get('call_time_diff'))) {
            $data['call_time_diff'] = (int) Input::get('call_time_diff') * 60;
        }
        $data['message']    = Input::get('message');
        $data['type']       = $type;
        $data['active']     = true;
        $data['updated_at'] = date('Y-m-d H:i:s');

        Hook::query()->where('id', '=', $id)->update($data);

        return redirect('/hooks');
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(int $id)
    {
        $hook = Hook::query()->where('id', '=', $id)->limit(1)->get();

        if ($hook[0]->isOwner(Auth::id())) {
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
