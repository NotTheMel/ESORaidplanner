<?php

namespace App\Http\Middleware;

use App\Comment;
use App\Guild;
use Auth;
use Closure;

class CommentOwnerMiddleware
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
        $slug       = $request->route('slug');
        $comment_id = $request->route('comment_id');
        /** @var Guild $guild */
        $guild   = Guild::query()->where('slug', '=', $slug)->first();
        $comment = Comment::query()->find($comment_id);

        if (null === $guild || null === $comment || (!$guild->isAdmin(Auth::user()) && $comment->user_id !== Auth::id())) {
            return redirect('/g/'.$slug);
        }

        return $next($request);
    }
}
