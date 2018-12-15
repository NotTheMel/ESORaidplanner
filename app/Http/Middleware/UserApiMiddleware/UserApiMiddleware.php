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

namespace App\Http\Middleware\UserApiMiddleware;

use App\Event;
use App\Guild;
use App\Signup;
use App\User;
use Illuminate\Http\Request;

class UserApiMiddleware
{
    protected function getRequestUser(Request $request): ?User
    {
        $header = str_replace('Basic ', '', $request->header('Authorization'));
        $header = explode(':', base64_decode($header), 2);

        return User::query()->where('email', '=', $header[0])->first();
    }

    protected function getRequestGuild(Request $request): ?Guild
    {
        $guild_id = null;

        if (!empty($request->route('guild_id')) || !empty($request->input('guild_id'))) {
            $guild_id = $request->route('guild_id') ?? $request->input('guild_id');
        } elseif (!empty($request->route('event_id'))) {
            $event    = Event::query()->find($request->route('event_id'));
            $guild_id = $event->guild_id;
        } elseif (!empty($request->route('signup_id'))) {
            $signup   = Signup::query()->find($request->route('signup_id'));
            $guild_id = $signup->event->guild_id;
        }

        return Guild::query()->find($guild_id);
    }
}
