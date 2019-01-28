@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">Settings for {{ $guild->name }}</h4>
                            <p class="category">{{ $guild->platform() }} - {{ $guild->megaserver() }}</p>
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
                        </div>
                    </div>

                    @if ($guild->isOwner(Auth::user()))
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Delete{{ $guild->name }}</h4>
                                <p class="category">{{ $guild->platform() }} - {{ $guild->megaserver() }}</p>
                            </div>
                            <div class="content">
                                <a href="/g/{{ $guild->slug }}/delete">
                                    <button type="button" class="btn btn-danger">Delete Guild</button>
                                </a>
                            </div>
                        </div>
                    @endif

                    <div class="card">
                        <div class="header">
                            <h4 class="title">Discord bot</h4>
                        </div>
                        <div class="content">
                            @if($guild->hasDiscordBot())
                                <div class="alert alert-success">
                                    <p>The Discord bot is connected to your guild.</p>
                                </div>
                                <a href="{{ route('guildDisconnectDiscordBot', ['slug' => $guild->slug]) }}"
                                   target="_blank">
                                    <button type="button" class="btn btn-danger">Disconnect Discord bot!</button>
                                </a>
                            @else
                                <div class="alert alert-danger">
                                    <p>The Discord bot is not connected to your guild. Add the Discord bot by clicking
                                        the button below. Once the bot is added type '!setup' in the channel you want
                                        the bot to function in (make sure the bot has read/write/embed permissions in
                                        that channel!).</p>
                                </div>
                                <a href="https://discordapp.com/oauth2/authorize?client_id=479385645224165406&scope=bot&permissions=215040"
                                   target="_blank">
                                    <button type="button" class="btn btn-success">Add the Discord bot!</button>
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="card">
                        <div class="header">
                            <h4 class="title">iCal link</h4>
                        </div>
                        <div class="content">
                            Add the events of this guild to your favorite calendar with this iCal link<br>
                            {!! Form::text('ical', env('APP_URL').'/api/ical/guild/'.$guild->createIcalUid(Auth::user()), array('class' => 'form-control', 'readonly')) !!}
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
                                @foreach($guild->repeatableEvents()->orderBy('name')->get()->all() as $repeatable)
                                    <tr>
                                        <td>{{ $repeatable->name }}</td>
                                        <td>{{ $repeatable->getRepetitionString() }}</td>
                                        <td>
                                            {{--<a href="{{ '/g/' . $guild->slug . '/repeatable/edit/' . $repeatable->id }}">--}}
                                                {{--<button type="button" class="btn btn-info">Edit</button>--}}
                                            {{--</a>--}}
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

                <div class="col-md-6">
                    <div class="card">
                        <div class="header">
                            <div class="pull-right">
                                <a href="{{ route('notificationMessageTypeSelectView', ['slug' => $guild->slug]) }}">
                                    <button class="btn btn-success" type="button">Create new</button>
                                </a>
                            </div>
                            <h4 class="title">Configured Notifications</h4>
                            <p class="category"></p>
                        </div>
                        <div class="content table-responsive table-full-width">
                            <table class="table  table-striped">
                                <thead>
                                <th>Name</th>
                                <th>Type</th>
                                <th>System</th>
                                </thead>
                                <tbody>
                                @foreach ($guild->notifications()->get()->all() as $notification)
                                    <tr>
                                        <td>
                                                {{ $notification->name }}
                                            </td>
                                        <td>{{ $notification->getMessageTypeConfig()['name'] }}</td>
                                        <td>{{ $notification->getSystemName() }}</td>
                                        <td>
                                            <a title="Send test message" href="{{ route('notificationSendTest', ['slug' => $guild->slug, 'notification_id' => $notification->id]) }}">
                                                <i class="fa fa-envelope"></i>
                                            </a>
                                            <a title="Edit" href="{{ route('notificationUpdateView', ['slug' => $guild->slug, 'notification_id' => $notification->id]) }}">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a title="Delete" href="{{ route('notificationDelete', ['slug' => $guild->slug, 'notification_id' => $notification->id]) }}">
                                                <i class="fa fa-ban"></i>
                                            </a>
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
