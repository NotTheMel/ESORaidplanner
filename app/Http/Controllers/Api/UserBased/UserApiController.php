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

namespace App\Http\Controllers\Api\UserBased;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class UserApiController extends Controller
{
    protected function getRequestUser(Request $request): User
    {
        $header = str_replace('Basic ', '', $request->header('Authorization'));
        $header = explode(':', base64_decode($header), 2);

        return User::query()->where('email', '=', $header[0])->first();
    }
}
