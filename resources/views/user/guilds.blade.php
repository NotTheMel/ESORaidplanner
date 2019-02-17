@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="row">

            @include('user.partials.side_menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="header">
                        <h4 class="title">Guilds for {{ Auth::user()->name }}</h4>
                    </div>
                    <div class="content">
                        <div class="content table-responsive table-full-width">
                            <table class="table  table-striped">
                                <thead>
                                <th width="70%">Name</th>
                                <th width="30%">Actions</th>
                                </thead>
                                <tbody>
                                @foreach (Auth::user()->guilds() as $guild)
                                    <tr>
                                        <td>{{ $guild->name }}</td>
                                        <td>
                                            <a href="{{ '/g/' . $guild->slug }}">
                                                <button type="button" class="btn btn-info">View guild</button>
                                            </a>
                                            @if(!$guild->isOwner(Auth::user()))
                                                <a href="{{ '/g/' . $guild->slug . '/member/leave' }}">
                                                    <button type="button" class="btn btn-danger">Leave guild</button>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection