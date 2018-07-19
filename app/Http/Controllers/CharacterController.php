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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class CharacterController extends Controller
{
    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function create(Request $request)
    {
        $data            = $request->all();
        $data['user_id'] = Auth::id();

        if (!empty(Input::get('sets'))) {
            $data['sets'] = implode(', ', $request->input('sets'));
        } else {
            $data['sets'] = '';
        }

        $character = new Character($data);
        $character->save();

        return redirect('/profile/characters');
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function edit(Request $request, int $id)
    {
        $character = Character::query()
            ->where('id', '=', $id)
            ->where('user_id', '=', Auth::id())
            ->first();

        if (empty($character)) {
            return redirect('/profile/character');
        }

        $data = $request->all();

        if (!empty(Input::get('sets'))) {
            $data['sets'] = implode(', ', $request->input('sets'));
        } else {
            $data['sets'] = '';
        }

        $character->update($data);

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

        return redirect('/profile/characters');
    }
}
