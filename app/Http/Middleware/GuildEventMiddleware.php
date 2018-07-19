<?php

namespace App\Http\Middleware;

use App\Event;
use App\Guild;
use Closure;

class GuildEventMiddleware
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
        $slug     = $request->route('slug');
        $event_id = $request->route('event_id');
        /** @var Guild $guild */
        $guild = Guild::query()->where('slug', '=', $slug)->first();
        /** @var Event $event */
        $event = Event::query()->find($event_id);

        if (null === $guild || null === $event || $guild->id !== $event->guild_id) {
            return redirect('/');
        }

        return $next($request);
    }
}
