<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Response;

class ApiAuth
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

        $valid = Auth::validate(['email' => $header[0], 'password' => $header[1]]);

        if (!$valid) {
            return response('Invalid credentials', Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
