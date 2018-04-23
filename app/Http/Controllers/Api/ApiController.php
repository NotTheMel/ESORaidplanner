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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    /**
     * Simple true/false login check.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function checkLogin(Request $request): JsonResponse
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        $u = [];

        $u['name']   = $user->name;
        $u['email']  = $user->email;
        $u['layout'] = $user->layout;

        return response($u, Response::HTTP_OK);
    }

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
