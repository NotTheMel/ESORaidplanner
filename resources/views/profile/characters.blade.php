@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <div class="pull-right">
                            <a href="{{ '/profile/menu' }}">
                                <button type="button" class="btn">Back to user menu</button>
                            </a>
                        </div>
                        <h4 class="title">Character presets for {{ Auth::user()->name }}</h4>
                    </div>
                    <div class="content">
                        <div class="content table-responsive table-full-width">
                            <table class="table table-hover table-striped">
                                <thead>
                                <th width="25%">Name</th>
                                <th width="25%">Class</th>
                                <th width="25%">Role</th>
                                <th width="25%">Sets</th>
                                </thead>
                                <tbody>
                                @foreach (Auth::user()->getCharacters() as $character)
                                    <tr>
                                        <td>
                                            {{ $character->name }}
                                            @if ($character->public === 1)
                                                <i class="fa fa-group"
                                                   title="This character is publicly visible on your profile">
                                            @endif
                                        </td>
                                        <td>{{ $character->getClassName() }}</td>
                                        <td>{{ $character->getRoleName() }}</td>
                                        <td>{{ $character->sets }}</td>
                                        <td>
                                            {{ Form::open(array('url' => '/profile/character/delete/'.$character->id)) }}
                                            {!! Form::open([]) !!}
                                            {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}

                                            {!! Form::close() !!}
                                            {{ Form::close() }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{ Form::open(array('url' => '/profile/character/create')) }}
                        {!! Form::open([]) !!}
                        <div class="row">
                            <div class="col-md-4">
                                Character
                                name:{!! Form::text('name', '', array('class' => 'form-control', 'required' => 'required')) !!}
                                <br>
                            </div>
                            <div class="col-md-4">
                                Class:
                                {!! Form::select('class', array('1' => 'Dragonknight', '2' => 'Sorcerer', '3' => 'Nightblade', '4' => 'Warden', '6' => 'Templar'), null, array('class' => 'form-control')) !!}
                            </div>
                            <div class="col-md-4">
                                Role:
                                {!! Form::select('role', array('1' => 'Tank', '2' => 'Healer', '3' => 'Damage Dealer (Magicka)', '4' => 'Damage Dealer (Stamina)', '5' => 'Other'), null, array('class' => 'form-control')) !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                Supportive sets:
                                {!! Form::select('sets[]', $sets, null, array('class' => 'chosen-select', 'multiple')) !!}
                                <br><br>
                                This character is publicly visible on my
                                profile: {!! Form::checkbox('public', 1, true); !!}
                                <br><br>
                                {!! Form::submit('Create character preset', ['class' => 'btn']) !!}

                                {!! Form::close() !!}
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection