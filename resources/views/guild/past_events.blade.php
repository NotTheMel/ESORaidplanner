@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <div class="pull-right">
                            <a href="{{ '/g/' . $guild->slug }}">
                                <button class="btn btn-primary" type="button">Back to {{ $guild->name }}</button>
                            </a>
                        </div>
                        <h4 class="title">Past events for {{ $guild->name }}</h4>
                    </div>
                    <div class="content">
                        <table class="table">
                            <th width="15%">Date and Time</th>
                            <th>Event</th>
                            <th>Signups</th>
                            @foreach ($events as $event)
                                <tr>
                                    <td>{{ $event->getUserHumanReadableDate() }}</td>
                                    <td>
                                        <a href="{{ route('eventDetailView', ['slug' => $guild->slug, 'event_id' => $event->id]) }}">{{ $event->name }}</a>
                                    </td>
                                    <td>{{ \count($event->signups) }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection