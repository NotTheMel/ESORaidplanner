<?php

namespace App\Http\Middleware;

use App\Guild;
use App\Hook\NotificationHook;
use Auth;
use Closure;

class HookOwnerMiddleware
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
        $id   = $request->route('hook_id');
        $hook = NotificationHook::query()->find($id);

        if (null === $hook) {
            return redirect('/');
        }

        $guild = Guild::query()->find($hook->guild_id);

        if (!$guild->isAdmin(Auth::user())) {
            return redirect('/');
        }

        return $next($request);
    }
}
