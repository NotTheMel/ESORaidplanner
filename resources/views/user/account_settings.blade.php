@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="row">

            @include('user.partials.side_menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="header">
                        <h4 class="title">Your account settings</h4>
                    </div>
                    <div class="content">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{ Form::open(array('url' => route('userUpdateAccountSettings'))) }}

                        Email:{!! Form::text('email', Auth::user()->email, array('class' => 'form-control', 'required' => 'required')) !!}
                        <br>

                        Display
                        name:{!! Form::text('name', Auth::user()->name, array('class' => 'form-control', 'required' => 'required')) !!}
                        <br>

                        New password:{!! Form::password('password', array('class' => 'form-control')) !!}<br>

                        Repeat new
                        password:{!! Form::password('password_repeat', array('class' => 'form-control')) !!}<br>

                        Timezone: {!! Form::select('timezone', \App\Utility\UserDateHandler::timeZones(), Auth::user()->timezone, array('class' => 'form-control')) !!}
                        <br>

                        Clock
                        format: {!! Form::select('clock', ['12' => '12 hour clock', '24' => '24 hour clock'], Auth::user()->clock, array('class' => 'form-control')) !!}
                        <br>

                        Preferred layout
                        color:{!! Form::select('layout', ['0' => 'Crow\'s Wood (purple)', '1' => 'Clockwork City (orange)', '2' => 'Blessed Crucible (red)', '3' => 'Greenshade (green)', '4' => 'Orsinium (blue)', '5' => 'Sanctum Ophidia (dark green)'], Auth::user()->layout, array('class' => 'form-control')) !!}
                        <br>

                        Telegram username (optional):
                        {!! Form::text('telegram_username', Auth::user()->telegram_username, array('class' => 'form-control')) !!}
                        <br>

                        Discord Handle (optional):
                        {!! Form::text('discord_handle', Auth::user()->discord_handle, array('class' => 'form-control')) !!}
                        <br>

                        {!! Form::submit('Save changes', ['class' => 'btn btn-info']) !!}<br>

                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection