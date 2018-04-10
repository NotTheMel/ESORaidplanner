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
                            {{ Form::open(array('url' => 'guild/create')) }}

                            Guild name:{!! Form::text('name', '', array('class' => 'form-control')) !!}<br>

                            Guild slug (this will be your guild link. Only lowercase
                            letters):{!! Form::text('slug', '', array('class' => 'form-control', 'pattern' => '[A-Za-z0-9]+')) !!}
                            <br>

                            Guild
                            megaserver:{!! Form::select('megaserver', array('1' => 'EU', '2' => 'NA'), null, array('class' => 'form-control')) !!}
                            <br>

                            Guild
                            Platform:{!! Form::select('platform', array('1' => 'PC/Mac', '2' => 'Playstation 4', '3' => 'XBOX One'), null, array('class' => 'form-control')) !!}
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