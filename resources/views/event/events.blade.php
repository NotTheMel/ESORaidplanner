@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <div class="pull-right">
                            <a href="{{ '/g/' . $guild->slug }}">
                                <button type="button">Back to {{ $guild->name }}</button>
                            </a>
                        </div>
                        <h4 class="title">Past events for {{ $guild->name }}</h4>
                    </div>
                    <div class="content">
                        <table class="table">
                            <th>Date and Time</th>
                            <th>Event</th>
                            <th>Signups</th>
                            @foreach ($events as $event)
                                <tr>
                                    <td>
                                        <a href="{{ '/g/' . $guild->slug . '/event/' . $event->id }}">{{ $event->name }}</a>
                                    </td>
                                    <td>{{ $event->getNiceDate() }}</td>
                                    <td>{{ $event->getTotalSignups() }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection