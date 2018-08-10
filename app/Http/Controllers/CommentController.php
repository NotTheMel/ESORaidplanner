<?php

/**
 * This file is part of the ESO Raidplanner project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/ESORaidplanner/ESORaidplanner
 */

namespace App\Http\Controllers;

use App\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * @param Request $request
     * @param string  $slug
     * @param int     $event_id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function create(Request $request, string $slug, int $event_id)
    {
        $comment           =  new Comment();
        $comment->event_id = $event_id;
        $comment->user_id  = Auth::id();
        $comment->text     = $request->input('text');
        $comment->save();

        return redirect('/g/'.$slug.'/event/'.$event_id);
    }

    /**
     * @param Request $request
     * @param string  $slug
     * @param int     $event_id
     * @param int     $comment_id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function edit(Request $request, string $slug, int $event_id, int $comment_id)
    {
        $comment = Comment::query()
            ->where('id', '=', $comment_id)
            ->where('user_id', '=', Auth::id())
            ->first();

        $comment->text = $request->input('text');
        $comment->save();

        return redirect('/g/'.$slug.'/event/'.$event_id);
    }

    /**
     * @param string $slug
     * @param int    $event_id
     * @param int    $comment_id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(string $slug, int $event_id, int $comment_id)
    {
        Comment::query()->where('id', '=', $comment_id)->delete();

        return redirect('/g/'.$slug.'/event/'.$event_id);
    }
}
