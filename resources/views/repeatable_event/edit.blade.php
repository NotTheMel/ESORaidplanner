@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">Edit recurring event {{ $repeatable->name }}</h4>
                        </div>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="content">
                            {{ Form::open(array('url' => '/g/' . $guild->slug . '/repeatable/edit/'.$repeatable->id)) }}

                            Event name:{!! Form::text('name', $repeatable->name, array('class' => 'form-control')) !!}<br>

                            Event
                            type:{!! Form::select('type', array('1' => 'Trials', '2' => 'Dungeons', '3' => 'PvP', '4' => 'Guild Meeting', '6' => 'Other'), $repeatable->type, array('class' => 'form-control')) !!}
                            <br>

                            Event tags (separate each tag with a comma, using tags will make sure this event will onlt trigger notifications that have a matching tag):
                            {!! Form::text('tags', $repeatable->tags, ['class' => 'form-control']) !!}<br><br>
                            Description:{!! Form::textarea('description', $repeatable->description, array('class' => 'form-control')) !!}<br>
                            Repeat this event{!! Form::select('interval', \App\Singleton\EventTimeIntervals::INTERVALS, $repeatable->interval, array('class' => 'form-control', 'required')) !!}<br>
                            How many days in advance should this event be created? (max 28 days, aka 4 weeks):{!! Form::number('create_interval', ($repeatable->create_interval / 86400), array('min' => '0', 'max' => '28', 'class' => 'form-control', 'required')) !!}

                            {!! Form::submit('Save', ['class' => 'btn']) !!}<br>

                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection