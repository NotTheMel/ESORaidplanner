<?php

namespace App\Http\Middleware\UserApiMiddleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $header = str_replace('Basic ', '', $request->header('Authorization'));
        $header = explode(':', base64_decode($header), 2);

        if (!Auth::attempt(['email' => $header[0], 'password' => $header[1]])) {
            return \response('Incorrect credentials.', Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
