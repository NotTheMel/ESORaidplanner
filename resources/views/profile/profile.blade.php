@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-user">
                    <div class="card-image" style="max-height: 350px; overflow: hidden"><img
                                src="/storage/covers/{{ $user->cover_image }}"
                                alt="..." width="100%"></div> <!---->
                    <div class="card-body">
                        <div class="author"><a href="#"><img src="/storage/avatars/{{ $user->avatar }}"
                                                             alt="..."
                                                             class="avatar border-gray">
                                <h4 class="title">{{ $user->name }}<br>
                                    <small>{{ $user->title }}</small>
                                </h4>
                            </a></div>
                        <p class="description text-center"> {!! $user->description !!}
                        </p>
                        <br></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="header">
                        <h4 class="title text-center">Pledges Allegiance to</h4>
                        <p align="center">
                            @if (!empty($user->getAlliance()))
                                <img style="height: 241px"
                                     src="{{ asset('/img/alliances/' . $user->getAlliance()->image) }}"><br>
                                <b>The {{ $user->getAlliance()->name }}</b>
                            @else
                                <img style="height: 241px" src="{{ asset('/img/none.png') }}"><br>
                                <b>None</b>
                            @endif
                        </p><br>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="header">
                        <h4 class="title text-center">Masters the Skill of</h4>
                        <p align="center">
                            @if (!empty($user->getClass()))
                                <img style="height: 241px"
                                     src="{{ asset('/img/classes/' . $user->getClass()->image) }}"><br>
                                <b>The {{ $user->getClass()->name }}</b>
                            @else
                                <img style="height: 241px"
                                     src="{{ asset('/img/none.png') }}"><br>
                                <b>None</b>
                            @endif
                        </p><br>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="header">
                        <h4 class="title text-center">Representing the Race of</h4>
                        <p align="center">
                            @if (!empty($user->getRace()))
                                <img style="height: 241px"
                                     src="{{ asset('/img/races/' . $user->getRace()->image) }}"><br>
                                <b>The {{ $user->getRace()->plural }}</b>
                            @else
                                <img style="height: 241px"
                                     src="{{ asset('/img/none.png') }}"><br>
                                <b>None</b>
                            @endif
                        </p><br>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="header">
                        <h4 class="title text-center">Character Presets</h4>
                    </div>
                    <div class="content">
                        <div class="content table-responsive table-full-width">
                            <table class="table table-hover table-striped">
                                <thead>
                                <th width="25%">Name</th>
                                <th width="25%">Class</th>
                                <th width="25%">Role</th>
                                <th width="25%">Sets</th>
                                </thead>
                                <tbody>
                                @foreach ($characters as $character)
                                    <tr>
                                        <td>{{ $character->name }}</td>
                                        <td>{{ $character->getClassName() }}</td>
                                        <td>{{ $character->getRoleName() }}</td>
                                        <td>{!! $character->getSetsFormatted() !!}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            {{--<div class="col-md-4">--}}
            {{--<div class="card">--}}
            {{--<div class="header">--}}
            {{--<h4 class="title text-center">Badges</h4>--}}
            {{--</div>--}}
            {{--<div class="content">--}}
            {{--<?php $i = 0; ?>--}}
            {{--@foreach($badges as $badge)--}}

            {{--@if ($i === 0)--}}
            {{--<div class="row">--}}
            {{--@endif--}}

            {{--<div class="col-md-6">--}}
            {{--<p align="center">--}}
            {{--<img width="200px" src="{{ asset('/img/badges/' . $badge->image) }}"--}}
            {{--title="{{ $badge->description }}"><br>--}}
            {{--<b>{{ $badge->name }}</b><br>--}}
            {{--Earned {{ $badge->getNiceDate() }}--}}
            {{--</p>--}}
            {{--</div>--}}

            {{--@if ($i === 1)--}}
            {{--</div>--}}
            {{--@endif--}}

            {{--<?php $i++; ?>--}}
            {{--@if ($i === 2)--}}
            {{--<?php $i = 0; ?>--}}
            {{--@endif--}}
            {{--@endforeach--}}

            {{--@if ($i === 1)--}}
            {{--</div>--}}
            {{--@endif--}}
            {{--</div>--}}
            {{--</div>--}}
        </div>
    </div>

@endsection