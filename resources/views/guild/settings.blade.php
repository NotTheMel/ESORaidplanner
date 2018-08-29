@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">Settings for {{ $guild->name }}</h4>
                            <p class="category">{{ $guild->getPlatform() }} - {{ $guild->getMegaserver() }}</p>
                        </div>
                        <div class="content">
                            {{ Form::open(array('url' => '/g/' . $guild->slug . '/settings')) }}
                            {!! Form::open([]) !!}
                            Discord
                            Widget:{{ Form::textarea('discord_widget', $guild->discord_widget, ['class' => 'form-control', 'size' => '50x5']) }}
                            <br>
                            {!! Form::submit('Save', ['class' => 'btn btn-success']) !!}
                            {!! Form::close() !!}
                            {{ Form::close() }}

                            @if ($guild->isOwner(Auth::user()))
                                <br><br><br><br><a href="{{ '/guild/delete/' . $guild->id }}">
                                    <button type="button" class="btn btn-danger">Delete Guild</button>
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="card">
                        <div class="header">
                            <h4 class="title">Discord bot</h4>
                        </div>
                        <div class="content">
                            @if($guild->hasDiscordBot())
                                <div class="alert alert-success">
                                    <p>The Discord bot is connected to your guild.</p>
                                </div>
                            @else
                                <div class="alert alert-danger">
                                    <p>The Discord bot is not connected to your guild. Add the Discord bot by clicking the button below. Once the bot is added type '!setup' in the channel you want the bot to function in (make sure the bot has read/write/embed permissions in that channel!).</p>
                                </div>
                                <a href="https://discordapp.com/oauth2/authorize?client_id=479385645224165406&scope=bot&permissions=215040" target="_blank">
                                    <button type="button" class="btn btn-success">Add the Discord bot!</button>
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="card">
                        <div class="header">
                            <div class="pull-right">
                                <a href="{{ '/g/' . $guild->slug . '/repeatable/create' }}">
                                    <button type="button" class="btn btn-info">Create a recurring event</button>
                                </a>
                            </div>
                            <h4 class="title">Recurring events for {{ $guild->name }}</h4>
                        </div>
                        <div class="content table-responsive table-full-width">
                            <table class="table  table-striped">
                                <thead>
                                <th>Name</th>
                                <th>Interval</th>
                                </thead>
                                <tbody>
                                @foreach($guild->getRepeatableEvents() as $repeatable)
                                    <tr>
                                        <td>{{ $repeatable->name }}</td>
                                        <td>{{ $repeatable->getRepetitionString() }}</td>
                                        <td>
                                            <a href="{{ '/g/' . $guild->slug . '/repeatable/edit/' . $repeatable->id }}">
                                                <button type="button" class="btn btn-info">Edit</button>
                                            </a>
                                            <a href="{{ '/g/' . $guild->slug . '/repeatable/delete/' . $repeatable->id }}">
                                                <button type="button" class="btn btn-danger">Delete</button>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="content">
                                <div class="alert alert-danger">
                                    <p>Take note that deleting a recurring event will not delete any of the events that
                                        it
                                        created. It will just stop new events from being created.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($guild->isAdmin(Auth::user()) || Auth::user()->global_admin === 1)
                    <div class="col-md-6">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Membership requests</h4>
                                <p class="category"></p>
                            </div>
                            <div class="content table-responsive table-full-width">
                                <table class="table  table-striped">
                                    <thead>
                                    <th>Name</th>
                                    <th>Role</th>
                                    </thead>
                                    <tbody>
                                    @foreach ($guild->getPendingMembers() as $member)
                                        <tr>
                                            <td>{{ $guild->getMemberName($member->id) }}</td>
                                            <td>Membership pending</td>
                                            @if ($guild->isAdmin(Auth::user()) || Auth::user()->global_admin === 1)
                                                <td>
                                                    {{ Form::open(array('url' => '/g/' . $guild->slug . '/member/approve/'.$guild->id.'/'.$member->id)) }}
                                                    {!! Form::open([]) !!}
                                                    {!! Form::submit('Approve', ['class' => 'btn btn-success']) !!}

                                                    {!! Form::close() !!}
                                                    {{ Form::close() }}
                                                    {{ Form::open(array('url' => '/g/' . $guild->slug . '/member/remove/'.$guild->id.'/'.$member->id)) }}
                                                    {!! Form::open([]) !!}
                                                    {!! Form::submit('Remove', ['class' => 'btn btn-danger']) !!}

                                                    {!! Form::close() !!}
                                                    {{ Form::close() }}
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                @endif

            </div>

        </div>
    </div>
@endsection
