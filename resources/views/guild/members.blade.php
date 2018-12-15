@extends('layouts.app')

@section('content')
    <div class="content">

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
                        @if (count($guild->users(\App\Guild::MEMBERSHIP_STATUS_PENDING) ) > 0)
                            <table class="table  table-striped">
                                <thead>
                                <th>Name</th>
                                <th>Role</th>
                                </thead>
                                <tbody>
                                @foreach ($guild->users(\App\Guild::MEMBERSHIP_STATUS_PENDING) as $member)
                                    <tr>
                                        <td>{{ $member->name }}</td>
                                        <td>Membership pending</td>
                                        <td>
                                            <a href="{{ route('guildApproveMember', ['slug' => $guild->slug, 'user_id' => $member->id]) }}">
                                                <button class="btn btn-primary">Approve</button>
                                            </a>
                                            <a href="{{ route('guildRemoveMember', ['slug' => $guild->slug, 'user_id' => $member->id]) }}">
                                                <button class="btn btn-danger">Decline</button>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="content">
                                <p>No pending members.</p>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

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
                            @foreach ($guild->users(\App\Guild::MEMBERSHIP_STATUS_MEMBER) as $member)
                                <tr>
                                    <td>{{ $member->name }}</td>
                                    @if ($guild->isOwner($member))
                                        <td>Owner</td>
                                    @elseif ($guild->isAdmin($member))
                                        <td>Admin</td>
                                    @else
                                        <td>Member</td>
                                    @endif
                                    @if (Auth::id() === $member->id)
                                        <td></td>
                                        <td></td>
                                    @else
                                        <td width="40%">
                                            @if ($guild->isOwner(Auth::user()) && !$guild->isAdmin($member))
                                                <a href="{{ route('guildAddAdmin', ['slug' => $guild->slug, 'user_id' => $member->id]) }}">
                                                    <button type="button" class="btn btn-info">Promote to admin
                                                    </button>
                                                </a>
                                            @elseif ($guild->isOwner(Auth::user()) && $guild->isAdmin($member))
                                                <a href="{{ route('guildRemoveAdmin', ['slug' => $guild->slug, 'user_id' => $member->id]) }}">
                                                    <button type="button" class="btn btn-info">Demote admin</button>
                                                </a>
                                            @endif
                                            @if (!$guild->isOwner($member))
                                                <a href="{{ route('guildRemoveMember', ['slug' => $guild->slug, 'user_id' => $member->id]) }}">
                                                    <button class="btn btn-danger">Remove</button>
                                                </a>
                                            @endif
                                        </td>
                                        <td></td>
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