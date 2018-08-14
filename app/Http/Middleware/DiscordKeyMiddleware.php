<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class DiscordKeyMiddleware
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
        $token = str_replace('Basic ', '', $request->header('Authorization'));
        $token = base64_decode($token);

        if ($token !== env('DISCORD_BOT_TOKEN')) {
            return response('Invalid token.', Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
