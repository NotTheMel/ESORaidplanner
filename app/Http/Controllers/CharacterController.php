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

use App\Character;
use App\Signup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class CharacterController extends Controller
{
    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function create()
    {
        $character          = new Character();
        $character->name    = Input::get('name');
        $character->class   = Input::get('class');
        $character->role    = Input::get('role');
        $character->user_id = Auth::id();
        $character->public  = Input::get('public') ?? 0;

        if (!empty(Input::get('sets'))) {
            $character->sets = implode(', ', Input::get('sets'));
        } else {
            $character->sets = '';
        }

        $character->save();

        return redirect('/profile/characters');
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function modify(int $id)
    {
        $character = Character::query()
            ->where('id', '=', $id)
            ->where('user_id', '=', Auth::id())
            ->first();

        if (empty($character)) {
            return redirect('/profile/character');
        }

        $character->name  = Input::get('name');
        $character->role  = Input::get('role');
        $character->class = Input::get('class');
        if (!empty(Input::get('sets'))) {
            $character->sets = implode(', ', Input::get('sets'));
        } else {
            $character->sets = '';
        }

        $character->public = Input::get('public') ?? 0;

        $character->save();

        return redirect('/profile/character');
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(int $id)
    {
        $character = Character::query()
            ->where('id', '=', $id)
            ->where('user_id', '=', Auth::id())
            ->first();

        if (empty($character)) {
            return redirect('/profile/characters');
        }

        $character->delete();

        Signup::query()
            ->where('character_id', '=', $id)
            ->update(['character_id' => null]);

        return redirect('/profile/characters');
    }
}
