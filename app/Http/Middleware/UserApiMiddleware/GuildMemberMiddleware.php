<?php

namespace App\Http\Middleware\UserApiMiddleware;

use Closure;
use Illuminate\Http\Response;

class GuildMemberMiddleware extends UserApiMiddleware
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

        $user = $this->getRequestUser($request);

        if (null === $guild) {
            return response('This guild does not exist.', Response::HTTP_BAD_REQUEST);
        }
        if ($guild->isPendingMember($user)) {
            return response('Membership for this guild is pending.', Response::HTTP_UNAUTHORIZED);
        }
        if (!$guild->isMember($user)) {
            return response('You are not a member of this guild.', Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
