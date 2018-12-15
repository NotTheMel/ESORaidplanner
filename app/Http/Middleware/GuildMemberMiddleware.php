<?php

namespace App\Http\Middleware;

use App\Guild;
use Closure;
use Illuminate\Support\Facades\Auth;

class GuildMemberMiddleware
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

        if (null === $guild) {
            return redirect('/');
        }
        if ($guild->isPendingMember(Auth::user())) {
            return redirect('/g/'.$guild->slug.'/pending');
        }
        if (!$guild->isMember(Auth::user())) {
            return redirect('/g/'.$guild->slug.'/apply');
        }

        return $next($request);
    }
}
