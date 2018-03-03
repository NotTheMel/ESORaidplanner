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

use App\Avatar;
use App\Character;
use App\Set;
use App\Singleton\TimeZones;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

/**
 * Class UserController.
 */
class UserController extends Controller
{
    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        $timezones = TimeZones::list();

        return view('auth.register', compact('timezones'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editProfilePage()
    {
        $timezones = TimeZones::list();

        $sets = $this->getSets();

        return view('profile.edit', compact('timezones', 'sets'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function editProfile()
    {
        $username        = Input::get('username');
        $email           = Input::get('email');
        $password        = Input::get('password');
        $password_repeat = Input::get('password_repeat');
        $timezone        = Input::get('timezone');
        $clock           = Input::get('clock');
        $layout          = Input::get('layout');
        $telegram        = str_replace('@', '', Input::get('telegram_username'));

        $timezones = TimeZones::list();

        if ($password !== $password_repeat) {
            $error = 'Given passwords do not match';

            $sets = $this->getSets();

            return view('profile.edit', compact('timezones', 'error', 'sets'));
        }

        if ($email !== Auth::user()->email && count(User::query()->where('email', '=', $email)->get()) > 0) {
            $error = 'A user with that email address already exists.';

            $sets = $this->getSets();

            return view('profile.edit', compact('timezones', 'error', 'sets'));
        }

        $values                      = [];
        $values['name']              = $username;
        $values['email']             = $email;
        $values['timezone']          = $timezone;
        $values['clock']             = $clock;
        $values['layout']            = $layout;
        $values['telegram_username'] = $telegram;
        if (!empty($password)) {
            $values['password'] = bcrypt($password);
        }

        User::query()->where('id', '=', Auth::id())->update($values);

        return redirect('/profile/accountsettings');
    }

    public function profileEdit()
    {
        $r        = DB::table('races')->orderBy('name')->get();
        $races    = [];
        $races[0] = 'None';

        foreach ($r as $race) {
            $races[$race->id] = $race->name;
        }

        $c          = DB::table('classes')->orderBy('id')->get();
        $classes    = [];
        $classes[0] = 'None';

        foreach ($c as $class) {
            $classes[$class->id] = $class->name;
        }

        $a            = DB::table('alliances')->orderBy('id')->get();
        $alliances    = [];
        $alliances[0] = 'None';

        foreach ($a as $alliance) {
            $alliances[$alliance->id] = $alliance->name;
        }

        return view('profile.profile_settings', compact('races', 'classes', 'alliances'));
    }

    public function profileEditPost(Request $request)
    {
        $request->validate([
            'race'     => 'required',
            'alliance' => 'required',
            'class'    => 'required',
        ]);

        $user = User::query()->find(Auth::id());

        $user->race        = Input::get('race');
        $user->class       = Input::get('class');
        $user->alliance    = Input::get('alliance');
        $user->description = Input::get('description') ?? '';
        if ($user->membership_level > 0) {
            $user->title = Input::get('title') ?? '';
        }

        $user->save();

        return redirect('/profile/'.$user->id);
    }

    public function menuProfilePage()
    {
        return view('profile.menu');
    }

    public function profileCharacters()
    {
        $sets = $this->getSets();

        return view('profile.characters', compact('sets'));
    }

    public function profileMembership()
    {
        return view('profile.membership');
    }

    public function avatarEditPage()
    {
        $avatars = Avatar::query()->orderBy('name', 'asc')->get();

        return view('profile.avatar_select', compact('avatars'));
    }

    public function editAvatar()
    {
        User::query()->where('id', '=', Auth::id())->update(['avatar' => Input::get('avatar')]);

        return redirect('/profile/edit/avatar');
    }

    public function uploadAvatar(Request $request)
    {
        if (Auth::user()->membership_level < 1) {
            return redirect('/profile/edit/avatar');
        }

        $this->validate($request, [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,JPG|max:2048',
        ]);

        $request->file('avatar')->store('public/avatars');

        // ensure every image has a different name
        $file_name = $request->file('avatar')->hashName();

        // save new image $file_name to database
        User::query()->where('id', '=', Auth::id())->update(['avatar' => $file_name]);

        return redirect('/profile/edit/avatar');
    }

    public function profile(int $user_id)
    {
        $user = User::query()->find($user_id);

        $characters = Character::query()
            ->where('user_id', '=', $user_id)
            ->where('public', '=', 1)
            ->orderBy('name', 'asc')
            ->get();

        $badges = $user->getBadges();

        return view('profile.profile', compact('user', 'characters', 'badges'));
    }

    /**
     * @return array
     */
    private function generate_timezone_list()
    {
        return TimeZones::list();
    }

    /**
     * @return array
     */
    private function getSets(): array
    {
        $sets_q = Set::query()->orderBy('name', 'asc')->get();

        $sets = [];

        foreach ($sets_q as $set) {
            $sets[$set->name] = $set->name;
        }

        return $sets;
    }
}
