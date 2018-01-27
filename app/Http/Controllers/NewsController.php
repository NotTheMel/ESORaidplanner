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
 * @see https://github.com/Woeler/eso-raid-planner
 */

namespace App\Http\Controllers;

use App\NewsArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function create(Request $request)
    {
        if (1 !== Auth::user()->global_admin) {
            return redirect('/');
        }

        $request->validate([
            'image'   => 'required|image|mimes:jpeg,png,jpg,JPG|max:2048',
            'title'   => 'required',
            'content' => 'required',
        ]);

        $article            = new NewsArticle();
        $article->title     = Input::get('title');
        $article->content   = Input::get('content');
        $article->author_id = Auth::id();

        $request->file('image')->store('public/news');

        // ensure every image has a different name
        $article->image = $request->file('image')->hashName();

        $article->save();

        return redirect('/news/article/'.$article->id);
    }

    /**
     * @param Request $request
     * @param int     $article_id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function modify(Request $request, int $article_id)
    {
        if (1 !== Auth::user()->global_admin) {
            return redirect('/');
        }

        $request->validate([
            'title'   => 'required',
            'content' => 'required',
        ]);

        $article          = NewsArticle::query()->find($article_id);
        $article->title   = Input::get('title');
        $article->content = Input::get('content');

        if ($request->hasFile('image')) {
            $request->file('image')->store('public/news');

            Storage::delete('news/'.$article->image);
            // ensure every image has a different name
            $article->image = $request->file('image')->hashName();
        }

        $article->save();

        return redirect('/news/article/'.$article->id);
    }

    /**
     * @param Request $request
     * @param int     $article_id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(Request $request, int $article_id)
    {
        if (1 !== Auth::user()->global_admin) {
            return redirect('/');
        }

        $article = NewsArticle::query()->find($article_id);

        Storage::delete('news/'.$article->image);

        $article->delete();

        return redirect('/');
    }
}
