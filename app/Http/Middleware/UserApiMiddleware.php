<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserApiMiddleware
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
        $valid  = Auth::validate(['email' => $header[0], 'password' => $header[1]]);

        if (!$valid) {
            return response('Invalid login credentials', Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
