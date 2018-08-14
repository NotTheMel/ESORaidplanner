<?php

namespace App\Http\Middleware;

use App\Event;
use App\Guild;
use App\User;
use Closure;
use Illuminate\Http\Response;

class DiscordMiddleware
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
        $guild_id = $request->input('guild_id');
        $user_id  = $request->input('user_handle');
        $event_id = $request->input('event_id') ?? null;

        /** @var Guild $guild */
        $guild = Guild::query()->where('discord_id', '=', $guild_id)->first();
        $user  = User::query()->where('discord_handle', '=', $user)->first();

        if (null === $guild) {
            return response('I do not know your guild. Make sure the GM configures it correctly.', Response::HTTP_BAD_REQUEST);
        }
        if (null === $user) {
            return response('I do not know you. Make sure to set your Discord handle in your ESO Raidplanner profile.', Response::HTTP_BAD_REQUEST);
        }
        if (null !== $event_id) {
            /** @var Event $event */
            $event = Event::query()->find($event_id);
            if (null === $event || $event->guild_id !== $guild->id) {
                return response('The event you are trying to sign up for does not exist');
            }
        }

        return $next($request);
    }
}
