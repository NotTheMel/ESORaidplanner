@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <div class="pull-right">
                            <a href="{{ '/g/' . $guild->slug . '/teams'}}">
                                <button type="button" class="btn btn-info">Back to teams</button>
                            </a>
                        </div>
                        <h4 class="title">Team: {{ $team->name }}</h4>
                        <p class="category"></p>
                    </div>
                    <div class="content table-responsive table-full-width">
                        <table class="table  table-striped">
                            <thead>
                            <th>Name</th>
                            </thead>
                            <tbody>
                            @foreach ($team->getMembers() as $member)
                                <tr>
                                    <td>
                                        <a href="#">
                                            {{ $member->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="/g/{{ $guild->slug }}/team/{{ $team->id }}/removemember/{{ $member->id }}">
                                            <button type="button" class="btn btn-danger">Remove</button>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                    {{ Form::open(array('url' => '/g/'.$guild->slug.'/team/'.$team->id.'/addmember', 'method' => 'post')) }}
                    {!! Form::open([]) !!}
                    <div class="content">
                        <div id="row">
                            <div class="col-md-12">
                                {!! Form::select('user_id', $members, null, array('class' => 'form-control')) !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                Class
                                {!! Form::select('class_id', array('1' => 'Dragonknight', '2' => 'Sorcerer', '3' => 'Nightblade', '4' => 'Warden', '6' => 'Templar'), null, array('class' => 'form-control')) !!}
                            </div>
                            <div class="col-md-3">
                                Role
                                {!! Form::select('role_id', array('1' => 'Tank', '2' => 'Healer', '3' => 'Damage Dealer (Magicka)', '4' => 'Damage Dealer (Stamina)', '5' => 'Other'), null, array('class' => 'form-control')) !!}
                            </div>
                            <div class="col-md-4">
                                Supportive sets<br>
                                {!! Form::select('sets[]', $sets, null, array('class' => 'chosen-select', 'multiple')) !!}
                            </div>
                            <div class="col-md-2">
                                <br>{!! Form::submit('Add to team', ['class' => 'btn btn-info']) !!}
                                <br>
                            </div>
                        </div>
                    </div>

                    {!! Form::close() !!}
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection