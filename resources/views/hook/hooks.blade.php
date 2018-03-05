@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-9">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">Notifications</h4>
                        </div>
                        <div class="content">
                            <a href="{{ '/hooks/create/1' }}">
                                <button type="button" class="btn">Create a Discord notification</button>
                            </a>
                            <a href="{{ '/hooks/create/2' }}">
                                <button type="button" class="btn">Create a Telegram notification</button>
                            </a>
                            <a href="{{ '/hooks/create/3' }}">
                                <button type="button" class="btn">Create a Slack notification</button>
                            </a>
                            <br>
                            <div class="content table-responsive table-full-width">
                                <table class="table table-hover table-striped">
                                    <thead>
                                    <th>Name</th>
                                    <th>Integration</th>
                                    <th>Owner</th>
                                    </thead>
                                    <tbody>
                                    @foreach ($hooks as $hook)
                                        <tr>
                                            <td>
                                                <a href="{{ url('hooks/modify/'.$hook->type . '/' . $hook->id) }}">{{ $hook->name }}</a>
                                            </td>
                                            <td>
                                                {{ $hook->getHookType() }}
                                            </td>
                                            @if ($hook->user_id === Auth::id())
                                                <td>You</td>
                                            @else
                                                <td>{{ $hook->getGuild()->name }} (Guild)</td>
                                            @endif
                                            <td>
                                                {{ Form::open(array('url' => '/hooks/delete/' . $hook->id)) }}
                                                {!! Form::open([]) !!}
                                                {!! Form::submit('Remove', ['class' => 'btn btn-danger']) !!}

                                                {!! Form::close() !!}
                                                {{ Form::close() }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                @include('parts.discordwidget')
            </div>
        </div>
    </div>
@endsection