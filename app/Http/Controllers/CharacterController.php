<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 11.10.18
 * Time: 13:43.
 */

namespace App\Http\Controllers;

use App\Character;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CharacterController
{
    public function createView()
    {
        return view('user.character_create');
    }

    public function updateView(int $character_id)
    {
        $character = Character::query()
            ->where('id', '=', $character_id)
            ->where('user_id', '=', Auth::id())
            ->first();

        if (null === $character) {
            return response('This character does not belong to you.', 401);
        }

        return view('user.character_update', compact('character'));
    }

    public function create(Request $request)
    {
        $character          = new Character($request->except(['sets']));
        $character->user_id = Auth::id();
        $character->public  = $request->input('public') ?? 0;
        $character->sets    = json_encode($request->input('sets') ?? []);
        $character->save();

        return redirect(route('userCharacterList'));
    }

    public function update(Request $request, int $character_id)
    {
        $character = Character::query()
            ->where('id', '=', $character_id)
            ->where('user_id', '=', Auth::id())
            ->first();
        $character->update($request->except(['sets']));
        $character->public = $request->input('public') ?? 0;
        $character->sets   = json_encode($request->input('sets') ?? []);
        $character->save();

        return redirect(route('userCharacterList'));
    }

    public function delete(int $character_id)
    {
        Character::query()
            ->where('id', '=', $character_id)
            ->where('user_id', '=', Auth::id())
            ->delete();

        return redirect(route('userCharacterList'));
    }
}
