<?php

namespace App\Http\Middleware\UserApiMiddleware;

use Closure;
use Illuminate\Http\Response;

class GuildAdminMiddleware extends UserApiMiddleware
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
        $guild = $this->getRequestGuild($request);
        $user  = $this->getRequestUser($request);

        if (null === $guild || !$guild->isAdmin($user)) {
            return response('You are not an admin of this guild.', Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
