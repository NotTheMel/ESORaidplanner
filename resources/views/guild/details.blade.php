@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-sm-6">
                    <div class="card card-stats">
                        <div class="content">
                            <div class="row">
                                <div class="col-xs-5">
                                    <div class="icon-big text-center icon-warning"><i
                                                class="fa fa-flag fa-3x"></i></div>
                                </div>
                                <div class="col-xs-7">
                                    <div class="numbers"><p>{{ $guild->name }}</p>
                                        <!-- react-text: 226 -->{{ $guild->platform() }}
                                        - {{ $guild->megaserver() }}
                                    <!-- /react-text --></div>
                                </div>
                            </div>
                            <div class="footer">
                                <hr>
                                <div class="stats"><i class="fa fa-refresh"></i><!-- react-text: 231 -->
                                    <!-- /react-text --><!-- react-text: 232 -->
                                    @if ($guild->isAdmin(Auth::user()))
                                        <a href="{{ '/g/' . $guild->slug . '/settings' }}">Go to Guild settings</a>
                                    @else
                                        <s>Go to Guild settings</s>
                                @endif
                                <!-- /react-text --></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card card-stats">
                        <div class="content">
                            <div class="row">
                                <div class="col-xs-5">
                                    <div class="icon-big text-center icon-warning"><i
                                                class="fa fa-group fa-3x"></i></div>
                                </div>
                                <div class="col-xs-7">
                                    <div class="numbers"><p>Members</p>
                                        <!-- react-text: 243 -->{{ count($guild->users(\App\Guild::MEMBERSHIP_STATUS_MEMBER)) }}
                                        ({{ count($guild->users(\App\Guild::MEMBERSHIP_STATUS_PENDING)) }} pending)
                                        <!-- /react-text --></div>
                                </div>
                            </div>
                            <div class="footer">
                                <hr>
                                <div class="stats"><i class="fa fa-calendar-o"></i>
                                    @if ($guild->isAdmin(Auth::user()))
                                        <a href="{{ '/g/' . $guild->slug . '/members' }}">Go to Members list</a>
                                    @else
                                        <s>Go to Members list</s>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card card-stats">
                        <div class="content">
                            <div class="row">
                                <div class="col-xs-5">
                                    <div class="icon-big text-center icon-warning"><i
                                                class="fa fa-calendar fa-3x"></i></div>
                                </div>
                                <div class="col-xs-7">
                                    <div class="numbers"><p>Total Events</p>
                                        <!-- react-text: 260 -->{{ \count($guild->events) }}
                                        ({{ count($guild->upcomingEvents()) }} upcoming)<!-- /react-text -->
                                    </div>
                                </div>
                            </div>
                            <div class="footer">
                                <hr>
                                <div class="stats"><i class="fa fa-clock-o"></i><!-- react-text: 265 -->
                                    <!-- /react-text --><!-- react-text: 266 --><a
                                            href="{{ '/g/' . $guild->slug . '/pastevents' }}">View past Events</a>
                                    <!-- /react-text -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card card-stats">
                        <div class="content">
                            <div class="row">
                                <div class="col-xs-5">
                                    <div class="icon-big text-center icon-warning"><i
                                                class="fa fa-book fa-3x"></i></div>
                                </div>
                                <div class="col-xs-7">
                                    <div class="numbers"><p>Logs</p>
                                        <!-- react-text: 277 -->{{ \count($guild->logs) }}<!-- /react-text -->
                                    </div>
                                </div>
                            </div>
                            <div class="footer">
                                <hr>
                                <div class="stats"><i class="fa fa-refresh"></i><!-- react-text: 282 -->
                                    <!-- /react-text --><!-- react-text: 283 -->
                                    @if ($guild->isAdmin(Auth::user()))
                                        <a href="{{ '/g/' . $guild->slug . '/logs' }}">Go to Guild logs</a>
                                    @else
                                        <s>Go to Guild logs</s>
                                @endif
                                <!-- /react-text --></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">


                @if (empty($guild->discord_widget))
                    <div class="col-md-12">
                        @else
                            <div class="col-md-9">
                                @endif
                                <div class="card">
                                    <div class="header">
                                        @if ($guild->isAdmin(Auth::user()))
                                            <div class="pull-right">
                                                <a href="{{ route('eventCreateView', ['slug' => $guild->slug]) }}">
                                                    <button type="button" class="btn btn-info">Create an Event</button>
                                                </a>
                                                <a href="{{ '/g/' . $guild->slug . '/repeatable/create' }}">
                                                    <button type="button" class="btn btn-info">Create a recurring
                                                        Event
                                                    </button>
                                                </a>
                                            </div>
                                        @endif
                                        <h4 class="title">{{ $guild->name }}</h4>
                                        <p class="category">{{ $guild->platform() }}
                                            - {{ $guild->megaserver() }}</p>
                                    </div>
                                    <div class="content table-responsive table-full-width">
                                        <table class="table  table-striped">
                                            <thead>
                                            <th>Event</th>
                                            <th>Date and Time</th>
                                            <th>Signups</th>
                                            </thead>
                                            <tbody>
                                            @foreach ($guild->upcomingEvents() as $event)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('eventDetailView', ['slug' => $guild->slug, 'event_id' => $event->id]) }}">{{ $event->name }}</a>
                                                    </td>
                                                    <td>{{ $event->getUserHumanReadableDate() }}</td>
                                                    <td>{{ \count($event->signups) }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>

                            @if(!empty($guild->discord_widget))
                                <div class="col-md-3">
                                    <div class="content">
                                        {!! $guild->discord_widget !!}
                                    </div>

                                </div>
                            @endif


                    </div>

            </div>
        </div>
@endsection
