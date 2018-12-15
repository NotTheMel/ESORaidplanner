@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="row">

            @include('user.partials.side_menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="header">
                        <h4 class="title">Your public profile</h4>
                    </div>
                    <div class="content">

                        @if (!empty($error))
                            <p class="text-warning">{{ $error }}</p><br>
                        @endif

                        {{ Form::open(array('url' => route('userUpdateProfileSettings'))) }}

                        Favorite
                        Race:{!! Form::select('race', \App\Utility\Races::RACES, Auth::user()->race, array('class' => 'form-control')) !!}
                        <br>
                        Favorite
                        Alliance:{!! Form::select('alliance', \App\Utility\Alliances::ALLIANCES, Auth::user()->alliance, array('class' => 'form-control')) !!}
                        <br>
                        Favorite
                        Class:{!! Form::select('class', \App\Utility\Classes::CLASSES, Auth::user()->class, array('class' => 'form-control')) !!}
                        <br>

                        @if (Auth::user()->membership_level > 0)
                            Title:{!! Form::text('title', Auth::user()->title, array('class' => 'form-control')) !!}<br>
                        @else
                            Title: *Profile titles are only available to Patreon
                            supporters{!! Form::text('title', Auth::user()->title, array('class' => 'form-control', 'readonly')) !!}
                            <br>
                        @endif

                        Bio:{!! Form::textarea('description', Auth::user()->description, array('class' => 'form-control')) !!}
                        <br>

                        {!! Form::submit('Save changes', ['class' => 'btn btn-info']) !!}<br>

                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection