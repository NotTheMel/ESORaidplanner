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

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    /**
     * Checks the credentials of a request coming in over the API.
     *
     * @param Request $request
     *
     * @return bool|mixed
     */
    protected function login(Request $request)
    {
        $header = str_replace('Basic ', '', $request->header('Authorization'));

        $header = explode(':', base64_decode($header), 2);

        $valid = Auth::validate(['email' => $header[0], 'password' => $header[1]]);

        if ($valid) {
            return User::query()->where('email', '=', $header[0])->first();
        }

        return false;
    }
}
