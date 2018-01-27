@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">

                <div class="col-md-12">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">Welcome to the ESO Raidplanner Beta!</h4>
                        </div>
                        <div class="content">
                            Welcome to the beta of the ESO Raidplanner. Please understand that this application is in a
                            state of testing. The purpose of this beta is to test, report bugs and do feature requests.
                            You can do these on my discord server. Make sure to ask me (@Woeler) to give you the beta
                            tester rank. Here is the link: <a href="https://discord.gg/TSHaB4q" target="_blank">https://discord.gg/TSHaB4q</a>.
                            Thank you for helping out!
                        </div>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">Events you are signed up for</h4>
                        </div>
                        <div class="content table-responsive table-full-width">
                            <table class="table table-hover table-striped">
                                <thead>
                                <th>Event</th>
                                <th>Date and Time</th>
                                <th>Guild</th>
                                </thead>
                                <tbody>
                                @foreach (Auth::user()->getEvents() as $event)
                                    @if($event->userIsSignedup())
                                        <tr>
                                            <td>
                                                <a href="{{ '/g/' . $event->getGuild()->slug . '/event/' . $event->id }}">{{ $event->name }}</a>
                                            </td>
                                            <td>{{ $event->getNiceDate() }}</td>
                                            <td>
                                                <a href="{{ '/g/' . $event->getGuild()->slug }}">{{ $event->getGuild()->name }}</a>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">Upcoming events</h4>
                        </div>
                        <div class="content table-responsive table-full-width">
                            <table class="table table-hover table-striped">
                                <thead>
                                <th>Event</th>
                                <th>Date and Time</th>
                                <th>Guild</th>
                                </thead>
                                <tbody>
                                @foreach (Auth::user()->getEvents() as $event)
                                    <tr>
                                        <td>
                                            <a href="{{ '/g/' . $event->getGuild()->slug . '/event/' . $event->id }}">{{ $event->name }}</a>
                                        </td>
                                        <td>{{ $event->getNiceDate() }}</td>
                                        <td>
                                            <a href="{{ '/g/' . $event->getGuild()->slug }}">{{ $event->getGuild()->name }}</a>
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
    </div>
@endsection
