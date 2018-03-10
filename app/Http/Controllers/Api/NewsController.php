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

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\NewsArticle;
use Illuminate\Http\Response;

class NewsController extends Controller
{
    public function all(): Response
    {
        $articles = NewsArticle::query()->orderBy('created_at', 'desc')->get()->all();

        return response($articles, Response::HTTP_OK);
    }

    public function get(int $article_id): Response
    {
        $article = NewsArticle::query()->find($article_id);

        return response($article, Response::HTTP_OK);
    }
}
