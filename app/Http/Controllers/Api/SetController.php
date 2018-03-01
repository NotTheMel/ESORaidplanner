<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 01.03.18
 * Time: 23:18.
 */

namespace App\Http\Controllers\Api;

use App\Set;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SetController extends ApiController
{
    /**
     * Get all gear sets known in the application.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function all(Request $request): JsonResponse
    {
        $user = $this->login($request);

        if (false === $user) {
            return response(null, 401);
        }

        return response(Set::query()->orderBy('name')->get(), 200);
    }

    /**
     * @return JsonResponse
     */
    public function getSetsVersion(): JsonResponse
    {
        return response(Set::query()->orderBy('version', 'desc')->first()->version, 200);
    }
}
