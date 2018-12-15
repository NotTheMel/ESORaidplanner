<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 11.10.18
 * Time: 11:40.
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function accountSettingsView()
    {
        return view('user.account_settings');
    }

    public function profileSettingsView()
    {
        return view('user.profile_settings');
    }

    public function characterListView()
    {
        return view('user.character_list');
    }

    public function avatarView()
    {
        return view('user.avatar_select');
    }

    public function icalView()
    {
        return view('user.ical');
    }

    public function updateAccountSettings(Request $request)
    {
        $request->validate([
            'name'           => 'required',
            'email'          => 'required|email',
            'password'       => 'same:password_repeat',
            'timezone'       => 'required',
            'clock'          => 'required',
            'layout'         => 'required',
            'discord_handle' => new \App\Rules\DiscordHandleRule(),
        ]);

        Auth::user()->update($request->except(['password', 'password_repeat']));

        if (!empty($request->input('password'))) {
            Auth::user()->password = bcrypt($request->input('password'));
        }

        Auth::user()->save();

        return redirect(route('userAccountSettingsView'));
    }

    public function updateProfileSettings(Request $request)
    {
        $request->validate([
            'race'     => 'required',
            'alliance' => 'required',
            'class'    => 'required',
        ]);

        Auth::user()->update($request->all());
        Auth::user()->save();

        return redirect(route('userProfileSettingsView'));
    }

    public function updateAvatar(Request $request)
    {
        if ($request->hasFile('avatar')) {
            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,JPG|max:2048',
            ]);

            $request->file('avatar')->store('public/avatars');
            $file_name           = $request->file('avatar')->hashName();
            Auth::user()->avatar = $file_name;
        } else {
            Auth::user()->avatar = $request->input('avatar');
        }
        Auth::user()->save();

        return redirect(route('userAvatarView'));
    }

    public function setNightMode(Request $request, int $mode)
    {
        Auth::user()->nightmode = $mode;
        Auth::user()->save();

        return redirect($request->get('url'));
    }
}
