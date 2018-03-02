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

namespace App\Http\Controllers\Api;

use App\Character;
use App\Event;
use App\Signup;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CharacterController extends ApiController
{
    /**
     * Gets all characters associated with the user.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function all(Request $request): JsonResponse
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        return response(Character::query()->where('user_id', '=', $user->id)->orderBy('name')->get() ?? [], 200);
    }

    /**
     * Create a character preset.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $character          = new Character();
        $character->name    = $request->input('name');
        $character->class   = $request->input('class');
        $character->role    = $request->input('role');
        $character->user_id = $user->id;
        $character->public  = $request->input('public') ?? 0;

        if (!empty($request->input('sets'))) {
            $character->sets = $request->input('sets');
        } else {
            $character->sets = '';
        }

        $character->save();

        return response(null, 200);
    }

    /**
     * Modify an existing character preset.
     *
     * @param Request $request
     * @param int     $character_id
     *
     * @return JsonResponse
     */
    public function edit(Request $request, int $character_id): JsonResponse
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $character = Character::query()->find($character_id);

        if ($character->user_id !== $user->id) {
            return response(null, 401);
        }

        $character->name    = $request->input('name') ?? $character->name;
        $character->class   = $request->input('class') ?? $character->class;
        $character->role    = $request->input('role') ?? $character->role;
        $character->user_id = $user->id;
        $character->public  = $request->input('public') ?? $character->public;
        $character->sets    = $request->input('sets') ?? $character->sets;

        $character->save();

        $signups = $user->getSignups();

        foreach ($signups as $signup) {
            $event = Event::query()->find($signup->event_id);

            $dt  = new DateTime($event->start_date);
            $now = new DateTime();

            if ($signup->character_id === $character->id && $dt > $now) {
                $sign           = Signup::query()->find($signup->id);
                $sign->class_id = $character->class;
                $sign->role_id  = $character->role;
                $sign->sets     = $character->sets;
                $sign->save();
            }
        }

        return response(null, 200);
    }

    /**
     * Delete a character preset.
     *
     * @param Request $request
     * @param int     $character_id
     *
     * @return JsonResponse
     */
    public function delete(Request $request, int $character_id): JsonResponse
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        $character = Character::query()->find($character_id);

        if ($character->user_id !== $user->id) {
            return response(null, 401);
        }

        $signups = $user->getSignups();

        foreach ($signups as $signup) {
            if ($signup->character_id === $character->id) {
                $sign               = Signup::query()->find($signup->id);
                $sign->character_id = null;
                $sign->save();
            }
        }

        $character->delete();

        return response(null, 200);
    }
}
