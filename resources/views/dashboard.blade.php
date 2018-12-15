@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">

                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="header">
                                    <h4 class="title">Events you are signed up for</h4>
                                </div>
                                <div class="content table-responsive table-full-width">
                                    <table class="table  table-striped">
                                        <thead>
                                        <th>Event</th>
                                        <th>Date and Time</th>
                                        <th>Guild</th>
                                        </thead>
                                        <tbody>
                                        @foreach (Auth::user()->UpcomingEvents() as $event)
                                            @if($event->isSignedUp(Auth::user()))
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('eventDetailView', ['slug' => $event->guild->slug, 'event_id' => $event->id]) }}">{{ $event->name }}</a>
                                                    </td>
                                                    <td>{{ $event->getUserHumanReadableDate() }}</td>
                                                    <td>
                                                        <a href="{{ '/g/' . $event->guild->slug }}">{{ $event->guild->name }}</a>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="header">
                                    <h4 class="title">Upcoming events</h4>
                                </div>
                                <div class="content table-responsive table-full-width">
                                    <table class="table  table-striped">
                                        <thead>
                                        <th>Event</th>
                                        <th>Date and Time</th>
                                        <th>Guild</th>
                                        </thead>
                                        <tbody>
                                        @foreach (Auth::user()->UpcomingEvents() as $event)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('eventDetailView', ['slug' => $event->guild->slug, 'event_id' => $event->id]) }}">{{ $event->name }}</a>
                                                </td>
                                                <td>{{ $event->getUserHumanReadableDate() }}</td>
                                                <td>
                                                    <a href="{{ '/g/' . $event->guild->slug }}">{{ $event->guild->name }}</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">Latest News</h4>
                        </div>
                        <div class="content table-responsive table-full-width">
                            <table class="table  table-striped">
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
                                        <td>{{ $article->getUserHumanReadableDate() }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
