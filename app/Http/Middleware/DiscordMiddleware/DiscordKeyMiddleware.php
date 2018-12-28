<?php

namespace App\Http\Middleware\DiscordMiddleware;

use App\User;
use App\Utility\DiscordBotMessages;
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
            ->whereNotNull('discord_id')
            ->where('discord_id', '=', $user_discord_long)
            ->first();
        if (null === $user) {
            $user = User::query()
                ->whereNotNull('discord_handle')
                ->where('discord_handle', '=', $user_id)
                ->first();
            if (null === $user) {
                return response(DiscordBotMessages::makeMention($user_discord_long).', I do not know you. Make sure to set your Discord handle in your ESO Raidplanner profile. Your Discord handle is `'.$user_id.'`. Please go here and set your handle: https://esoraidplanner.com/user/account-settings', Response::HTTP_BAD_REQUEST);
            }
        }

        $user->discord_handle = $user_id;
        $user->discord_id     = $user_discord_long;
        $user->save();

        return $next($request);
    }
}
