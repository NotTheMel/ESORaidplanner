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
use App\NewsArticle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::check()){
            $news = NewsArticle::query()->orderBy('created_at', 'desc')->limit(10)->get()->all();
            return view('dashboard', compact('news'));
        }
        return view('auth.login');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function dashboard()
    {
        $guild_ids = DB::table('user_guilds')->where('user_id', '=', Auth::id())->get();

        $guilds = [];

        foreach ($guild_ids as $guild_id) {
            $guild = Guild::query()->where('id', '=', $guild_id->guild_id)->get();

            $guilds[] = $guild[0];
        }

        return view('dashboard', compact('guilds'));
    }
}
