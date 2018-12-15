<?php

namespace App\Http\Middleware;

use App\Guild;
use Closure;

class GuildActiveMiddleware
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
        $slug = $request->route('slug');
        /** @var Guild $guild */
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (0 === $guild->active) {
            return redirect(route('guildInactiveView', ['slug' => $slug]));
        }

        return $next($request);
    }
}
