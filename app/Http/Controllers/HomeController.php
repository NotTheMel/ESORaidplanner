<?php

namespace App\Http\Controllers;

use App\NewsArticle;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $news = NewsArticle::query()->orderBy('created_at', 'desc')->limit(10)->get()->all();

        return view('dashboard', compact('news'));
    }

    public function showNews(int $article_id)
    {
        $article = NewsArticle::query()->find($article_id);
        $news    = NewsArticle::query()->orderBy('created_at', 'desc')->limit(10)->get()->all();

        return view('news.details', compact('article', 'news'));
    }
}
