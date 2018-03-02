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

use App\Set;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SetController extends ApiController
{
    /**
     * Get all gear sets known in the application.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function all(Request $request): Response
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        return response(Set::query()->orderBy('name')->get(), Response::HTTP_OK);
    }

    /**
     * @return Response
     */
    public function getVersion(): Response
    {
        return response(Set::query()->orderBy('version', 'desc')->first()->version, Response::HTTP_OK);
    }
}
