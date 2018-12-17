@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">{{ $event->name }}</h4>
                        </div>
                        <div class="content">

                            @if ($guild->isAdmin(Auth::user()))
                                <div class="pull-right">
                                    <a title="Edit" href="{{ '/g/' . $guild->slug . '/event/update/' . $event->id }}">
                                        <i class="fa fa-edit fa-2x fa-fw"></i>
                                    </a>
                                    <a title="Post signups"
                                       href="{{ '/g/' . $guild->slug . '/event/postsignups/' . $event->id }}">
                                        <i class="fa fa-envelope fa-2x fa-fw"></i>
                                    </a>
                                    @if($event->locked())
                                        <a title="Unlock"
                                           href="{{ '/g/' . $guild->slug . '/event/lock/' . $event->id . '/0' }}">
                                            <i class="fa fa-unlock fa-2x fa-fw"></i> </a>
                                    @else
                                        <a title="Lock"
                                           href="{{ '/g/' . $guild->slug . '/event/lock/' . $event->id . '/1' }}">
                                            <i class="fa fa-lock fa-2x fa-fw"></i> </a>
                                    @endif
                                    <a title="Delete" href="{{ '/g/' . $guild->slug . '/event/delete/' . $event->id }}"
                                       onclick="return confirm('Are you sure you want to delete this event?')">
                                        <i class="fa fa-ban fa-2x fa-fw"></i>
                                    </a>
                                    @if(true === false)
                                        <br>
                                        <div class="pull-right">
                                            <a href="{{ '/g/' . $guild->slug . '/event/' . $event->id . '/postsignups'}}">
                                                <button type="button" class="btn btn-success">Post signups</button>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <b>Description:</b> {{ $event->description }}<br>
                            <b>Starts:</b> {{ $event->getUserHumanReadableDate() }}<br>
                            <b>Total players signed up:</b> {{ $event->signups()->count() }}<br>
                            @if($guild->isAdmin(Auth::user()))
                                <b>Tags:</b>
                                @foreach($event->tags() as $tag)
                                    <span class="badge">{{ $tag }}</span>
                                @endforeach
                                <br>
                            @endif

                            @include('event.partials.signup_form')

                        </div>

                        @include('event.partials.signup_list')

                        @if($guild->isAdmin(Auth::user()))
                            @include('event.partials.admin_signup')
                        @endif

                    </div>
                </div>

                <div class="col-md-4">
                    @include('event.partials.comments')
                </div>
            </div>
        </div>
    </div>
@endsection