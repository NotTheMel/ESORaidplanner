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
                                    <a href="{{ '/g/' . $guild->slug . '/events/edit/' . $event->id }}">
                                        <button type="button" class="btn btn-info">Edit event</button>
                                    </a>
                                    @if($event->locked === 1)
                                        <a href="{{ '/g/' . $guild->slug . '/events/lock/' . $event->id . '/0' }}">
                                            <button type="button" class="btn btn-warning">Unlock event</button>
                                        </a>
                                    @else
                                        <a href="{{ '/g/' . $guild->slug . '/events/lock/' . $event->id . '/1' }}">
                                            <button type="button" class="btn btn-warning">Lock event</button>
                                        </a>
                                    @endif
                                    <a href="{{ '/g/' . $guild->slug . '/events/delete/' . $event->id }}">
                                        <button type="button" class="btn btn-danger">Delete event</button>
                                    </a>
                                    @if($guild->hasConfirmedSignupsHooks())
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
                            <b>Type:</b> {{ $event->getTypeName() }}<br>
                            <b>Starts:</b> {{ $event->getNiceDate() }}<br>
                            <b>Total players signed up:</b> {{ $event->getTotalSignups() }}<br>

                            @if($event->locked === 0)
                                @if ($event->userIsSignedUp())
                                    You are signed up for this event.<br>

                                    @if (count(Auth::user()->getCharacters(true)) === 0)
                                        <br>You do not have any character presets. Did you know you can create character
                                        presets in your <a href="/profile/characters">user profile</a> and use these to
                                        sign
                                        up
                                        faster?<br><br>
                                    @else
                                        <br><br><b>Use one of your presets to sign up</b><br>
                                        {{ Form::open(array('url' => 'g/' . $guild->slug . '/sign/modify/'.$event->id)) }}
                                        {!! Form::open([]) !!}
                                        <div class="row">
                                            <div class="col-md-10">
                                                Character preset
                                                @if (empty($event->getUserSignup('character_id')))
                                                    {!! Form::select('character', ['0' => 'You did not select a preset'] + Auth::user()->getCharacters(true), '0', array('class' => 'form-control')) !!}
                                                    <br>
                                                @else
                                                    {!! Form::select('character', Auth::user()->getCharacters(true), $event->getUserSignup('character_id'), array('class' => 'form-control')) !!}
                                                    <br>
                                                @endif
                                            </div>
                                            <div class="col-md-2">
                                                <br>{!! Form::submit('Save changes', ['class' => 'btn btn-info']) !!}
                                                <br>
                                            </div>
                                        </div>

                                        {!! Form::close() !!}
                                        {{ Form::close() }}
                                        <b>Or sign up using a custom setup</b><br>
                                    @endif

                                    {{ Form::open(array('url' => 'g/' . $guild->slug . '/sign/modify/'.$event->id)) }}
                                    {!! Form::open([]) !!}
                                    <div class="row">
                                        <div class="col-md-3">
                                            Class
                                            {!! Form::select('class', array('1' => 'Dragonknight', '2' => 'Sorcerer', '3' => 'Nightblade', '4' => 'Warden', '6' => 'Templar'), $event->getUserSignup('class_id'), array('class' => 'form-control')) !!}
                                        </div>
                                        <div class="col-md-3">
                                            Role
                                            {!! Form::select('role', array('1' => 'Tank', '2' => 'Healer', '3' => 'Damage Dealer (Magicka)', '4' => 'Damage Dealer (Stamina)', '5' => 'Other'), $event->getUserSignup('role_id'), array('class' => 'form-control')) !!}
                                        </div>
                                        <div class="col-md-4">
                                            Supportive sets<br>
                                            {!! Form::select('sets[]', $sets, explode(', ', $event->getUserSignup('sets')), array('class' => 'chosen-select', 'multiple')) !!}
                                        </div>
                                        <div class="col-md-2">
                                            <br>{!! Form::submit('Save changes', ['class' => 'btn btn-info']) !!}
                                            <br>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        {!! Form::close() !!}
                                        {{ Form::close() }}
                                        {{ Form::open(array('url' => 'g/' . $guild->slug . '/sign/off/'.$event->id)) }}
                                        {!! Form::open([]) !!}
                                        {!! Form::submit('Sign off', ['class' => 'btn btn-danger']) !!}<br>

                                        {!! Form::close() !!}
                                        {{ Form::close() }}
                                    </div>
                                @else
                                    @if (count(Auth::user()->getCharacters(true)) === 0)
                                        <br>You do not have any character presets. Did you know you can create character
                                        presets in your <a href="/profile/characters">user profile</a> and use these to
                                        sign
                                        up
                                        faster?<br><br>
                                    @else
                                        <br><br><b>Use one of your presets to sign up</b><br>
                                        {{ Form::open(array('url' => 'g/' . $guild->slug . '/sign/up/'.$event->id)) }}
                                        {!! Form::open([]) !!}
                                        <div class="row">
                                            <div class="col-md-10">
                                                Character preset
                                                {!! Form::select('character', Auth::user()->getCharacters(true), null, array('class' => 'form-control')) !!}
                                                <br>
                                            </div>
                                            <div class="col-md-2">
                                                <br>{!! Form::submit('Sign up', ['class' => 'btn btn-info']) !!}
                                                <br>
                                            </div>
                                        </div>

                                        {!! Form::close() !!}
                                        {{ Form::close() }}
                                        <b>Or sign up using a custom setup</b><br>
                                    @endif
                                    {{ Form::open(array('url' => 'g/' . $guild->slug . '/sign/up/'.$event->id)) }}
                                    {!! Form::open([]) !!}
                                    <div class="row">
                                        <div class="col-md-6">
                                            Class
                                            {!! Form::select('class', array('1' => 'Dragonknight', '2' => 'Sorcerer', '3' => 'Nightblade', '4' => 'Warden', '6' => 'Templar'), null, array('class' => 'form-control')) !!}
                                        </div>
                                        <div class="col-md-6">
                                            Role
                                            {!! Form::select('role', array('1' => 'Tank', '2' => 'Healer', '3' => 'Damage Dealer (Magicka)', '4' => 'Damage Dealer (Stamina)', '5' => 'Other'), null, array('class' => 'form-control')) !!}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            Supportive sets<br>
                                            {!! Form::select('sets[]', $sets, null, array('class' => 'chosen-select', 'multiple')) !!}
                                            &nbsp; {!! Form::submit('Sign up', ['class' => 'btn btn-info']) !!}
                                        </div>
                                    </div>

                                    {!! Form::close() !!}
                                    {{ Form::close() }}
                                @endif
                            @else
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-warning" role="alert">
                                            This event has been locked by an administrator of {{ $guild->name }}.
                                            Signing up, editing signups or signing off is not possible until an
                                            administrator unlocks this event.
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if($guild->isAdmin(Auth::user()))
                            {{ Form::open(array('url' => '/g/'.$guild->slug.'/event/'.$event->id.'signup/status')) }}
                        @endif

                        <div class="content table-responsive table-full-width" style="z-index: 9999">
                            <br>
                            <h3 align="center">Tanks</h3>
                            <table class="table  table-striped">
                                <thead>
                                @if ($guild->isAdmin(Auth::user()))
                                    <th width="20%">Player</th>
                                    <th width="15%">Class</th>
                                    <th width="20%">Role</th>
                                    <th width="20%">Sets</th>
                                    <th width="5%"></th>
                                    <th width="20%" colspan="3" class="text-center">Actions</th>
                                @else
                                    <th width="25%">Player</th>
                                    <th width="20%">Class</th>
                                    <th width="25%">Role</th>
                                    <th width="25%">Sets</th>
                                    <th width="5%"></th>
                                @endif
                                </thead>
                                <tbody>
                                @foreach ($tanks as $signup)
                                    @if ($signup->status === 1)
                                        <tr style="background-color: rgba(50, 205, 50, 0.5);">
                                    @elseif ($signup->status === 2)
                                        <tr style="background-color: rgba(255, 255, 0, 0.5);">
                                    @else
                                        <tr>
                                            @endif
                                            <td>{{ $signup->getUser()->name }}</td>
                                            <td>{{ $signup->getClassName() }}</td>
                                            <td>{{ $signup->getRoleName() }}</td>
                                            <td>{!! $signup->getSetsFormatted() !!}</td>
                                            <td><i class="fa fa-clock-o"
                                                   title="Signup time: {{ $signup->getNiceDate() }}"></td>
                                            @if ($guild->isAdmin(Auth::user()))
                                                <td align="center">
                                                    {{ Form::checkbox($signup->id, $signup->id) }}
                                                </td>
                                            @endif
                                        </tr>
                                        @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="content table-responsive table-full-width" style="z-index: 9999">
                            <h3 align="center">Healers</h3>
                            <table class="table  table-striped">
                                <thead>
                                @if ($guild->isAdmin(Auth::user()))
                                    <th width="20%">Player</th>
                                    <th width="15%">Class</th>
                                    <th width="20%">Role</th>
                                    <th width="20%">Sets</th>
                                    <th width="5%"></th>
                                    <th width="20%" colspan="3" class="text-center">Actions</th>
                                @else
                                    <th width="25%">Player</th>
                                    <th width="20%">Class</th>
                                    <th width="25%">Role</th>
                                    <th width="25%">Sets</th>
                                    <th width="5%"></th>
                                @endif
                                </thead>
                                <tbody>
                                @foreach ($healers as $signup)
                                    @if ($signup->status === 1)
                                        <tr style="background-color: rgba(50, 205, 50, 0.5);">
                                    @elseif ($signup->status === 2)
                                        <tr style="background-color: rgba(255, 255, 0, 0.5);">
                                    @else
                                        <tr>
                                            @endif
                                            <td>{{ $signup->getUser()->name }}</td>
                                            <td>{{ $signup->getClassName() }}</td>
                                            <td>{{ $signup->getRoleName() }}</td>
                                            <td>{!! $signup->getSetsFormatted() !!}</td>
                                            <td><i class="fa fa-clock-o"
                                                   title="Signup time: {{ $signup->getNiceDate() }}"></td>
                                            @if ($guild->isAdmin(Auth::user()))
                                                <td align="center">
                                                    {{ Form::checkbox($signup->id, $signup->id) }}
                                                </td>
                                            @endif
                                        </tr>
                                        @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="content table-responsive table-full-width" style="z-index: 9999">
                            <h3 align="center">Magicka DD's</h3>
                            <table class="table  table-striped">
                                <thead>
                                @if ($guild->isAdmin(Auth::user()))
                                    <th width="20%">Player</th>
                                    <th width="15%">Class</th>
                                    <th width="20%">Role</th>
                                    <th width="20%">Sets</th>
                                    <th width="5%"></th>
                                    <th width="20%" colspan="3" class="text-center">Actions</th>
                                @else
                                    <th width="25%">Player</th>
                                    <th width="20%">Class</th>
                                    <th width="25%">Role</th>
                                    <th width="25%">Sets</th>
                                    <th width="5%"></th>
                                @endif
                                </thead>
                                <tbody>
                                @foreach ($magickas as $signup)
                                    @if ($signup->status === 1)
                                        <tr style="background-color: rgba(50, 205, 50, 0.5);">
                                    @elseif ($signup->status === 2)
                                        <tr style="background-color: rgba(255, 255, 0, 0.5);">
                                    @else
                                        <tr>
                                            @endif
                                            <td>{{ $signup->getUser()->name }}</td>
                                            <td>{{ $signup->getClassName() }}</td>
                                            <td>{{ $signup->getRoleName() }}</td>
                                            <td>{!! $signup->getSetsFormatted() !!}</td>
                                            <td><i class="fa fa-clock-o"
                                                   title="Signup time: {{ $signup->getNiceDate() }}"></td>
                                            @if ($guild->isAdmin(Auth::user()))
                                                <td align="center">
                                                    {{ Form::checkbox($signup->id, $signup->id) }}
                                                </td>
                                            @endif
                                        </tr>
                                        @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="content table-responsive table-full-width" style="z-index: 9999">
                            <h3 align="center">Stamina DD's</h3>
                            <table class="table  table-striped">
                                <thead>
                                @if ($guild->isAdmin(Auth::user()))
                                    <th width="20%">Player</th>
                                    <th width="15%">Class</th>
                                    <th width="20%">Role</th>
                                    <th width="20%">Sets</th>
                                    <th width="5%"></th>
                                    <th width="20%" colspan="3" class="text-center">Actions</th>
                                @else
                                    <th width="25%">Player</th>
                                    <th width="20%">Class</th>
                                    <th width="25%">Role</th>
                                    <th width="25%">Sets</th>
                                    <th width="5%"></th>
                                @endif
                                </thead>
                                <tbody>
                                @foreach ($staminas as $signup)
                                    @if ($signup->status === 1)
                                        <tr style="background-color: rgba(50, 205, 50, 0.5);">
                                    @elseif ($signup->status === 2)
                                        <tr style="background-color: rgba(255, 255, 0, 0.5);">
                                    @else
                                        <tr>
                                            @endif
                                            <td>{{ $signup->getUser()->name }}</td>
                                            <td>{{ $signup->getClassName() }}</td>
                                            <td>{{ $signup->getRoleName() }}</td>
                                            <td>{!! $signup->getSetsFormatted() !!}</td>
                                            <td><i class="fa fa-clock-o"
                                                   title="Signup time: {{ $signup->getNiceDate() }}"></td>
                                            @if ($guild->isAdmin(Auth::user()))
                                                <td align="center">
                                                    {{ Form::checkbox($signup->id, $signup->id) }}
                                                </td>
                                            @endif
                                        </tr>
                                        @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if(count($others) > 0)
                            <div class="content table-responsive table-full-width" style="z-index: 9999">
                                <h3 align="center">Others</h3>
                                <table class="table  table-striped">
                                    <thead>
                                    @if ($guild->isAdmin(Auth::user()))
                                        <th width="20%">Player</th>
                                        <th width="15%">Class</th>
                                        <th width="20%">Role</th>
                                        <th width="20%">Sets</th>
                                        <th width="5%"></th>
                                        <th width="20%" colspan="3" class="text-center">Actions</th>
                                    @else
                                        <th width="25%">Player</th>
                                        <th width="20%">Class</th>
                                        <th width="25%">Role</th>
                                        <th width="25%">Sets</th>
                                        <th width="5%"></th>
                                    @endif
                                    </thead>
                                    <tbody>
                                    @foreach ($others as $signup)
                                        @if ($signup->status === 1)
                                            <tr style="background-color: rgba(50, 205, 50, 0.5);">
                                        @elseif ($signup->status === 2)
                                            <tr style="background-color: rgba(255, 255, 0, 0.5);">
                                        @else
                                            <tr>
                                                @endif
                                                <td>{{ $signup->getUser()->name }}</td>
                                                <td>{{ $signup->getClassName() }}</td>
                                                <td>{{ $signup->getRoleName() }}</td>
                                                <td>{!! $signup->getSetsFormatted() !!}</td>
                                                <td><i class="fa fa-clock-o"
                                                       title="Signup time: {{ $signup->getNiceDate() }}"></td>
                                                @if ($guild->isAdmin(Auth::user()))
                                                    <td align="center">
                                                        {{ Form::checkbox($signup->id, $signup->id) }}
                                                    </td>
                                                @endif
                                            </tr>
                                            @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        @if($guild->isAdmin(Auth::user()))
                            <div class="content">
                                <div class="pull-right">
                                    {!! Form::submit('Confirm selected', ['class' => 'btn btn-success', 'name' => 'action', 'value' => 'confirm']) !!}
                                    {!! Form::submit('Backup selected', ['class' => 'btn btn-warning', 'name' => 'action', 'value' => 'backup']) !!}
                                    {!! Form::submit('Reset selected', ['class' => 'btn btn-info', 'name' => 'action', 'value' => 'reset']) !!}
                                    {!! Form::submit('Delete selected', ['class' => 'btn btn-danger', 'name' => 'action', 'value' => 'delete']) !!}
                                </div>
                            </div>

                            {{ Form::close() }}
                            <br><br>
                        @endif

                        @if($guild->isAdmin(Auth::user()) && count($members) > 0)
                            <div class="content">
                                <strong>You are an admin.</strong> Here you can sign up someone else for this event.
                                {{ Form::open(array('url' => 'g/' . $guild->slug . '/sign/other/'.$event->id)) }}
                                {!! Form::open([]) !!}
                                <div id="row">
                                    <div class="col-md-12">
                                        {!! Form::select('user', $members, null, array('class' => 'form-control')) !!}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        Class
                                        {!! Form::select('class', array('1' => 'Dragonknight', '2' => 'Sorcerer', '3' => 'Nightblade', '4' => 'Warden', '6' => 'Templar'), null, array('class' => 'form-control')) !!}
                                    </div>
                                    <div class="col-md-3">
                                        Role
                                        {!! Form::select('role', array('1' => 'Tank', '2' => 'Healer', '3' => 'Damage Dealer (Magicka)', '4' => 'Damage Dealer (Stamina)', '5' => 'Other'), null, array('class' => 'form-control')) !!}
                                    </div>
                                    <div class="col-md-4">
                                        Supportive sets<br>
                                        {!! Form::select('sets[]', $sets, null, array('class' => 'chosen-select', 'multiple')) !!}
                                    </div>
                                    <div class="col-md-2">
                                        <br>{!! Form::submit('Sign up', ['class' => 'btn btn-info']) !!}
                                        <br>
                                    </div>
                                </div>

                                {!! Form::close() !!}
                                {{ Form::close() }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">Comments</h4>
                        </div>
                        <div class="content">
                            @foreach($comments as $comment)
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="thumbnail">
                                            <img class="img-responsive user-photo"
                                                 src="{{ asset('/storage/avatars/' . $comment->getUserAvatar()) }}">
                                        </div><!-- /thumbnail -->
                                    </div><!-- /col-sm-1 -->

                                    <div class="col-sm-10">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <strong>{{ $comment->getUserName() }}</strong> <span
                                                        class="text-muted">{{ $comment->getNiceDate() }}</span>
                                                @if ($comment->user_id === Auth::id())
                                                    <a href="{{ '/g/' . $guild->slug . '/event/' . $event->id . '/comment/delete/' . $comment->id }}">Remove</a>
                                                @endif
                                            </div>
                                            <div class="panel-body">
                                                {{  $comment->text }}
                                            </div><!-- /panel-body -->
                                        </div><!-- /panel panel-default -->
                                    </div>
                                </div>
                            @endforeach

                            {{ Form::open(array('url' => '/g/' . $guild->slug . '/event/' . $event->id . '/comment/create')) }}
                            {!! Form::open([]) !!}

                            {!! Form::textarea('text', '', ['class' => 'form-control', 'required']) !!}<br>

                            {!! Form::submit('Comment', ['class' => 'btn btn-info']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection