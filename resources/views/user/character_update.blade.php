@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="row">

            @include('user.partials.side_menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="header">
                        <h4 class="title">Update preset {{ $character->name }}</h4>
                    </div>
                    <div class="content">
                        {{ Form::open(['url' => route('characterUpdate', ['character_id' => $character->id]), 'method' => 'post']) }}

                        Character
                        name:{!! Form::text('name', $character->name, array('class' => 'form-control', 'required' => 'required')) !!}
                        <br>
                        Class:
                        {!! Form::select('class', \App\Utility\Classes::CLASSES, $character->class, array('class' => 'form-control')) !!}
                        <br>
                        Role:
                        {!! Form::select('role', \App\Utility\Roles::ROLES, $character->role, array('class' => 'form-control')) !!}
                        <br>
                        Supportive sets:
                        {!! Form::select('sets[]', \App\Set::query()->pluck('name', 'name'), $character->sets(), array('class' => 'chosen-select', 'multiple')) !!}
                        <br><br>
                        This character is publicly visible on my
                        profile: {!! Form::checkbox('public', 1, $character->isPublic()); !!}
                        <br><br>
                        {!! Form::submit('Update character preset', ['class' => 'btn btn-info']) !!}
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection