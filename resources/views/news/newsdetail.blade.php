@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">{{ $article->title }}</h4>
                        </div>
                        <div class="content">
                            {!! $article->content !!}
                            <div class="panel panel-default">
                                <div class="panel-body text-right">
                            {{ $article->getNiceDate() }} - Written by <b>{{ $article->getAuthor()->name }}</b>
                                    <img  class="img-rounded" width="50px" src="/storage/avatars/{{ $article->getAuthor()->avatar }}" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">Latest News</h4>
                        </div>
                        <div class="content table-responsive table-full-width">
                            <table class="table table-hover table-striped">
                                <thead>
                                <th>Title</th>
                                <th>Date</th>
                                </thead>
                                <tbody>
                                @foreach ($news as $article)
                                    <tr>
                                        <td>
                                            <a href="{{ '/news/' . $article->id }}">{{ $article->title }}</a>
                                        </td>
                                        <td>{{ $article->getNiceDate() }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @include('parts.discordwidget')
            </div>
        </div>
    </div>
@endsection