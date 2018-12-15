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

namespace App\Http\Controllers\Api\UserBased;

use App\Character;
use App\Event;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CharacterController extends UserApiController
{
    public function create(Request $request)
    {
        $user = $this->getRequestUser($request);

        $character          = new Character();
        $character->name    = $request->input('name');
        $character->class   = $request->input('class');
        $character->role    = $request->input('role');
        $character->user_id = $user->id;
        $character->public  = $request->input('public') ?? 0;

        $character->sets = $request->input('sets') ?? [];
        $character->save();

        return response($character, Response::HTTP_CREATED);
    }

    public function update(Request $request, int $character_id)
    {
        $user = $this->getRequestUser($request);

        $character = Character::query()->find($character_id);

        if ($character->user_id !== $user->id) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }
        $character->name    = $request->input('name') ?? $character->name;
        $character->class   = $request->input('class') ?? $character->class;
        $character->role    = $request->input('role') ?? $character->role;
        $character->user_id = $user->id;
        $character->public  = $request->input('public') ?? $character->public;
        $character->sets    = $request->input('sets') ?? $character->sets;
        $character->save();

        $signups = $user->signups;
        if (count($signups) > 0) {
            foreach ($signups as $signup) {
                /** @var Event $event */
                $event = Event::query()->find($signup->event_id);
                $dt    = new DateTime($event->start_date);
                $now   = new DateTime();
                if ($signup->character_id === $character->id && $dt > $now) {
                    $event->signupWithCharacter($user, $character);
                }
            }
        }

        return response($character, Response::HTTP_OK);
    }

    public function delete(Request $request, int $character_id)
    {
        $user = $this->getRequestUser($request);
        /** @var Character $character */
        $character = Character::query()->find($character_id);
        if ($character->user_id !== $user->id) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        $character->delete();

        return response(null, Response::HTTP_OK);
    }
}
