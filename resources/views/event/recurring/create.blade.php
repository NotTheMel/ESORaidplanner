@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">Create a recurring event for {{ $guild->name }}</h4>
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
                            {{ Form::open(array('url' => 'g/' . $guild->slug . '/repeatable/create')) }}

                            Event name:{!! Form::text('name', '', array('class' => 'form-control')) !!}<br>

                            <h5>Starting date and time</h5>
                            <div class="col-md-4">
                                <div class="form-group">
                                    Day:{!! Form::number('day', date('j'), array('min' => '1', 'max' => '31', 'class' => 'form-control')) !!}
                                    <br>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    Month:{!! Form::select('month', ['1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April', '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August', '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'], date('n'), array('class' => 'form-control')) !!}
                                    <br>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    Year:{!! Form::select('year', [date('Y') => date('Y'), date('Y') + 1 => date('Y') + 1], date('Y'), array('class' => 'form-control')) !!}
                                    <br>
                                </div>
                            </div>

                            @if (Auth::user()->clock === 12)
                                <div class="col-md-4">
                                    <div class="form-group">
                                        Hour:{!! Form::number('hour', null, array('min' => '1', 'max' => '12', 'class' => 'form-control')) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        Minute:{!! Form::number('minute', null, array('min' => '0', 'max' => '59', 'class' => 'form-control')) !!}
                                        <br>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        Meridiem:{!! Form::select('meridiem', ['am' => 'am', 'pm' => 'pm'], null, array('class' => 'form-control')) !!}
                                        <br>
                                    </div>
                                </div>
                            @else
                                <div class="col-md-6">
                                    <div class="form-group">
                                        Hour:{!! Form::number('hour', null, array('min' => '0', 'max' => '23', 'class' => 'form-control')) !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        Minute:{!! Form::number('minute', null, array('min' => '0', 'max' => '59', 'class' => 'form-control')) !!}
                                        <br>
                                    </div>
                                </div>
                            @endif
                            Timezone: {!! Form::select('timezone', \App\Utility\UserDateHandler::timeZones(), Auth::user()->timezone, array('class' => 'form-control')) !!}
                            Event tags (separate each tag with a comma, using tags will make sure this event will only trigger notifications that have a matching tag):
                            {!! Form::text('tags', '', ['class' => 'form-control']) !!}<br><br>
                            Description:{!! Form::textarea('description', '', array('class' => 'form-control')) !!}<br>
                            Repeat this event{!! Form::select('interval', ['DAILY' => 'Daily', 'WEEKLY' => 'Weekly', 'MONTHLY' => 'Monthly'], null, array('class' => 'form-control', 'required')) !!}<br>
                            How many events should be created in advance? (max 10):{!! Form::number('max_create_ahead', '1', array('min' => '1', 'max' => '10', 'class' => 'form-control', 'required')) !!}

                            {!! Form::submit('Create Repeatable Event', ['class' => 'btn btn-info']) !!}<br>

                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection