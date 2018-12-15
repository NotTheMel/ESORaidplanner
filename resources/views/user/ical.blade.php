@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="row">

            @include('user.partials.side_menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="header">
                        <h4 class="title">iCal settings</h4>
                    </div>
                    <div class="content">
                        Add all your events to your favorite calendar with this iCal link<br>
                        {!! Form::text('ical', env('APP_URL').'/api/ical/user/'.Auth::user()->createIcalUid(), array('class' => 'form-control', 'readonly')) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection