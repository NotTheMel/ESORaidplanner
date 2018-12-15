@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="row">

            @include('user.partials.side_menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="header">
                        <h4 class="title">Create a character preset</h4>
                    </div>
                    <div class="content">
                        {{ Form::open(['url' => route('characterCreate'), 'method' => 'post']) }}

                        Character
                        name:{!! Form::text('name', '', array('class' => 'form-control', 'required' => 'required')) !!}
                        <br>
                        Class:
                        {!! Form::select('class', \App\Utility\Classes::CLASSES, null, array('class' => 'form-control')) !!}
                        <br>
                        Role:
                        {!! Form::select('role', \App\Utility\Roles::ROLES, null, array('class' => 'form-control')) !!}
                        <br>
                        Supportive sets:
                        {!! Form::select('sets[]', \App\Set::query()->pluck('name', 'name'), null, array('class' => 'chosen-select', 'multiple')) !!}
                        <br><br>
                        This character is publicly visible on my
                        profile: {!! Form::checkbox('public', 1, true); !!}
                        <br><br>
                        {!! Form::submit('Create character preset', ['class' => 'btn btn-info']) !!}
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection