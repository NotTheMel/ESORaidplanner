<?php

namespace App\Http\Middleware;

use App\User;
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

        $user_discord_long = $request->input('discord_user_id');
        $user_id           = $request->input('discord_handle');
        $user              = User::query()
            ->whereNotNull('discord_handle')
            ->where('discord_handle', '=', $user_id)
            ->first();
        if (null === $user) {
            $user  = User::query()
                ->whereNotNull('discord_id')
                ->where('discord_id', '=', $user_discord_long)
                ->first();
            if (null === $user) {
                return response('I do not know you. Make sure to set your Discord handle in your ESO Raidplanner profile.', Response::HTTP_BAD_REQUEST);
            }
            $user->discord_handle = $user_id;
        }

        $user->discord_id = $user_discord_long;
        $user->save();

        return $next($request);
    }
}
