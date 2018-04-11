@extends('layouts.app')

@section('content')
    <div class="content">

        @if (($guild->isAdmin(Auth::user()) || Auth::user()->global_admin === 1) && count($pending ) > 0)
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">
                            <div class="pull-right">
                                <a href="{{ '/g/' . $guild->slug }}">
                                    <button type="button" class="btn btn-info">Back to {{ $guild->name }}</button>
                                </a>
                            </div>
                            <h4 class="title">Membership requests</h4>
                            <p class="category"></p>
                        </div>
                        <div class="content table-responsive table-full-width">
                            <table class="table  table-striped">
                                <thead>
                                <th>Name</th>
                                <th>Role</th>
                                </thead>
                                <tbody>
                                @foreach ($pending as $member)
                                    <tr>
                                        <td>{{ $member->name }}</td>
                                        <td>Membership pending</td>
                                        @if ($guild->isAdmin(Auth::user()) || Auth::user()->global_admin === 1)
                                            <td>
                                                {{ Form::open(array('url' => '/g/' . $guild->slug . '/member/approve/'.$guild->id.'/'.$member->id)) }}
                                                {!! Form::open([]) !!}
                                                {!! Form::submit('Approve', ['class' => 'btn btn-success']) !!}

                                                {!! Form::close() !!}
                                                {{ Form::close() }}
                                                <a href="{{ '/g/' . $guild->slug . '/member/remove/'.$guild->id.'/'.$member->id }}">
                                                    <button type="button" class="btn btn-danger">Remove</button>
                                                </a>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <div class="pull-right">
                            <a href="{{ '/g/' . $guild->slug }}">
                                <button type="button" class="btn btn-info">Back to {{ $guild->name }}</button>
                            </a>
                        </div>
                        <h4 class="title">Members of {{ $guild->name }}</h4>
                        <p class="category"></p>
                    </div>
                    <div class="content table-responsive table-full-width">
                        <table class="table  table-striped">
                            <thead>
                            <th>Name</th>
                            <th>Role</th>
                            <th></th>
                            <th></th>
                            </thead>
                            <tbody>
                            @foreach ($members as $member)
                                <tr>
                                    <td>{{ $member->name }}</td>
                                    @if ($guild->isOwner($member))
                                        <td>Owner</td>
                                    @elseif ($guild->isAdmin($member))
                                        <td>Admin</td>
                                    @else
                                        <td>Member</td>
                                    @endif
                                    @if ($guild->isAdmin(Auth::user()))
                                        @if (Auth::id() === $member->id)
                                            <td></td>
                                            <td></td>
                                        @else
                                            <td width="40%">
                                                @if ($guild->isOwner(Auth::user()) && !$guild->isAdmin($member))
                                                    <a href="{{ '/g/' . $guild->slug . '/member/makeadmin/'.$member->id }}">
                                                        <button type="button" class="btn btn-info">Promote to admin</button>
                                                    </a>
                                                @elseif ($guild->isOwner(Auth::user()) && $guild->isAdmin($member))
                                                    <a href="{{ '/g/' . $guild->slug . '/member/removeadmin/'.$member->id }}">
                                                        <button type="button" class="btn btn-info">Demote admin</button>
                                                    </a>
                                                @endif
                                                @if (!$guild->isOwner($member))
                                                    <a href="{{ '/g/' . $guild->slug . '/member/remove/'.$guild->id.'/'.$member->id }}">
                                                        <button type="button" class="btn btn-danger">Remove</button>
                                                    </a>
                                                @endif
                                            </td>
                                            <td></td>
                                        @endif
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @if (!$guild->isOwner(Auth::user()))
            <div class="row">
                <div class="col-md-2 pull-right">
                    <div class="card">
                        <div class="content">
                            <div>
                                <a href="{{ '/g/' . $guild->slug . '/member/leave' }}">
                                    <button type="button" class="btn btn-danger">Leave guild</button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection