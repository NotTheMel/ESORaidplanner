@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">Edit event {{ $event->name }}</h4>
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
                            {{ Form::open(array('url' => 'g/' . $guild->slug . '/events/edit/' . $event->id)) }}

                            Event name:{!! Form::text('name', $event->name, array('class' => 'form-control')) !!}<br>

                            Event
                            type:{!! Form::select('type', array('1' => 'Trials', '2' => 'Dungeons', '3' => 'PvP', '4' => 'Guild Meeting', '6' => 'Other'), $event->type, array('class' => 'form-control')) !!}
                            <br>

                            <div class="col-md-4">
                                <div class="form-group">
                                    Day:{!! Form::number('day', $start_date->format('j'), array('min' => '1', 'max' => '31', 'class' => 'form-control')) !!}
                                    <br>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    Month:{!! Form::select('month', ['1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April', '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August', '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'], $start_date->format('n'), array('class' => 'form-control')) !!}
                                    <br>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    Year:{!! Form::select('year', [date('Y') => date('Y'), date('Y') + 1 => date('Y') + 1], $start_date->format('Y'), array('class' => 'form-control')) !!}
                                    <br>
                                </div>
                            </div>

                            @if (Auth::user()->clock === 12)
                                <div class="col-md-4">
                                    <div class="form-group">
                                        Hour:{!! Form::number('hour', $start_date->format('g'), array('min' => '1', 'max' => '12', 'class' => 'form-control')) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        Minute:{!! Form::number('minute', $start_date->format('i'), array('min' => '0', 'max' => '59', 'class' => 'form-control')) !!}
                                        <br>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        Meridiem:{!! Form::select('meridiem', ['am' => 'am', 'pm' => 'pm'], $start_date->format('a'), array('class' => 'form-control')) !!}
                                        <br>
                                    </div>
                                </div>
                            @else
                                <div class="col-md-6">
                                    <div class="form-group">
                                        Hour:{!! Form::number('hour', $start_date->format('H'), array('min' => '0', 'max' => '23', 'class' => 'form-control')) !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        Minute:{!! Form::number('minute', $start_date->format('i'), array('min' => '0', 'max' => '59', 'class' => 'form-control')) !!}
                                        <br>
                                    </div>
                                </div>
                            @endif
                            Event tags (separate each tag with a comma, using tags will make sure this event will onlt trigger notifications that have a matching tag):
                            {!! Form::text('tags', $event->tags, ['class' => 'form-control']) !!}<br><br>
                            Description:{!! Form::textarea('description', $event->description, array('class' => 'form-control')) !!}
                            <br>

                            {!! Form::submit('Save Event', ['class' => 'btn']) !!}<br>

                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection