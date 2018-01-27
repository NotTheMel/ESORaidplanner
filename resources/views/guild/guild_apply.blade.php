@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">

                <div class="col-md-12">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">{{ $guild->name }}</h4>
                            <p class="category">{{ $guild->getPlatform() }}
                                - {{ $guild->getMegaserver() }}</p>
                        </div>
                        <div class="content">
                            You are not a member of {{ $guild->name }}.<br>
                            {{ Form::open(array('url' => '/g/' . $guild->slug . '/member/request/'.$guild->id)) }}
                            {!! Form::open([]) !!}
                            {!! Form::submit('Request membership', ['class' => 'btn']) !!}

                            {!! Form::close() !!}
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
