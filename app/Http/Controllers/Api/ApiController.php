<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 01.03.18
 * Time: 22:57.
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
