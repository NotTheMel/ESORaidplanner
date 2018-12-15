@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-9">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">Create a Guild</h4>
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
                            {{ Form::open(array('url' => route('guildCreate'), 'method' => 'post')) }}

                            Guild name:{!! Form::text('name', '', array('class' => 'form-control')) !!}<br>

                            Guild
                            megaserver:{!! Form::select('megaserver', \App\Utility\MegaServers::MEGASERVERS, null, array('class' => 'form-control')) !!}
                            <br>

                            Guild
                            Platform:{!! Form::select('platform', \App\Utility\Platforms::PLATFORMS, null, array('class' => 'form-control')) !!}
                            <br>

                            {!! Form::submit('Create Guild', ['class' => 'btn btn-info']) !!}<br>

                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
                @include('parts.discordwidget')
            </div>
        </div>
    </div>
@endsection