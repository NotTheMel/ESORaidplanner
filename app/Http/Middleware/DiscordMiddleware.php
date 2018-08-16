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
        $guild_id          = $request->input('discord_server_id');
        $user_discord_long = $request->input('discord_user_id');
        $event_id          = $request->input('event_id') ?? null;

        /** @var Guild $guild */
        $guild = Guild::query()->where('discord_id', '=', $guild_id)->first();
        $user  = User::query()->where('discord_id', '=', $user_discord_long)->first();

        if (null === $guild) {
            return response($user->getDiscordMention().', I do not know your guild. Make sure to set me up correctly using the !setup command.', Response::HTTP_BAD_REQUEST);
        }
        if (!$guild->isMember($user)) {
            return response($user->getDiscordMention().', you are not a member of '.$guild->name.'.', Response::HTTP_UNAUTHORIZED);
        }
        if (null !== $event_id) {
            /** @var Event $event */
            $event = Event::query()->find($event_id);
            if (null === $event || $event->guild_id !== $guild->id) {
                return response($user->getDiscordMention().', the event you are trying to sign up for does not exist');
            }
        }

        $guild->discord_last_activity = date('Y-m-d H:i:s');
        $guild->save();

        return $next($request);
    }
}
