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

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Patreon\API;
use Patreon\OAuth;

class PatreonController extends Controller
{
    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function OAuth()
    {
        $client_id     = env('PATREON_ID');
        $client_secret = env('PATREON_SECRET');

        $oauth_client = new OAuth($client_id, $client_secret);

        $redirect_uri  = 'https://esoraidplanner.com/patreon/login';
        $tokens        = $oauth_client->get_tokens($_GET['code'], $redirect_uri);
        $access_token  = $tokens['access_token'];
        $refresh_token = $tokens['refresh_token'];

        $api_client      = new API($access_token);
        $patron_response = $api_client->fetch_user();

        $patron = $patron_response->get('data') ?? 0;

        if (0 === $patron) {
            redirect('/patreon/error');
        }

        $pledge = null;

        if ($patron->has('relationships.pledges')) {
            try {
                $pledge = $patron->relationship('pledges')->get(0)->resolve($patron_response);
            } catch (\Exception $e) {
                return redirect('/patreon/error');
            }

            $cents = (int) $pledge->attribute('amount_cents');

            $u = User::query()->find(Auth::id());

            if ($cents >= 100 && $cents < 500) {
                $u->membership_level = 1;
            } elseif ($cents >= 500 && $cents < 1000) {
                $u->membership_level = 2;
            } elseif ($cents >= 1000 && $cents < 1500) {
                $u->membership_level = 3;
            } elseif ($cents >= 1500) {
                $u->membership_level = 4;
            }

            $u->save();

            $count = DB::table('user_patreon')->where('user_id', '=', Auth::id())->count();

            if (0 === $count) {
                DB::table('user_patreon')->insert([
                    'user_id'       => Auth::id(),
                    'access_token'  => $access_token,
                    'refresh_token' => $refresh_token,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ]);
            } else {
                DB::table('user_patreon')
                    ->where('user_id', '=', Auth::id())
                    ->update([
                    'access_token'  => $access_token,
                    'refresh_token' => $refresh_token,
                    'updated_at'    => date('Y-m-d H:i:s'),
                ]);
            }

            return redirect('/patreon/success');
        }

        return redirect('/patreon/error');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function view(): View
    {
        return view('patreon.login');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function error(): View
    {
        return view('patreon.error');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function success(): View
    {
        return view('patreon.success');
    }
}
