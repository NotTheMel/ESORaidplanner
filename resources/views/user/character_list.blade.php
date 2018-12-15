@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="row">

            @include('user.partials.side_menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="header">
                        <div class="pull-right">
                            <a href="{{ route('characterCreateView') }}">
                                <button type="button" class="btn btn-success">Create new preset</button>
                            </a>
                        </div>
                        <h4 class="title">Character presets for {{ Auth::user()->name }}</h4>
                    </div>
                    <div class="content">
                        <div class="content table-responsive table-full-width">
                            <table class="table  table-striped">
                                <thead>
                                <th width="15%">Name</th>
                                <th width="15%">Class</th>
                                <th width="15%">Role</th>
                                <th width="35%">Sets</th>
                                <th width="20%"></th>
                                </thead>
                                <tbody>
                                @foreach (Auth::user()->characters()->orderBy('name')->get()->all() as $character)
                                    <tr>
                                        <td>
                                            {{ $character->name }}
                                            @if ($character->isPublic())
                                                <i class="fa fa-group"
                                                   title="This character is publicly visible on your profile">
                                            @endif
                                        </td>
                                        <td>{{ $character->class() }}</td>
                                        <td>{{ $character->role() }}</td>
                                        <td>{{ $character->setsStringFormatted() }}</td>
                                        <td>
                                            <a href="{{ route('characterUpdate', ['character_id' => $character->id]) }}">
                                                <button type="button" class="btn btn-info">Edit</button>
                                            </a>
                                            <a href="{{ route('characterDelete', ['character_id' => $character->id]) }}">
                                                <button type="button" class="btn btn-danger">Delete</button>
                                            </a>
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