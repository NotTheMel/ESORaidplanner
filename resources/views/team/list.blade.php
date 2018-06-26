@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <div class="pull-right">
                            <a href="{{ '/g/' . $guild->slug . '/team/create' }}">
                                <button type="button" class="btn btn-success">Create a team</button>
                            </a>
                            <a href="{{ '/g/' . $guild->slug }}">
                                <button type="button" class="btn btn-info">Back to {{ $guild->name }}</button>
                            </a>
                        </div>
                        <h4 class="title">Teams</h4>
                        <p class="category"></p>
                    </div>
                    <div class="content table-responsive table-full-width">
                        <table class="table  table-striped">
                            <thead>
                            <th>Name</th>
                            <th>Member count</th>
                            </thead>
                            <tbody>
                            @foreach ($teams as $team)
                                <tr>
                                    <td>
                                        <a href="/g/{{ $guild->slug }}/team/{{ $team->id }}">
                                            {{ $team->name }}
                                        </a>
                                    </td>
                                    <td>{{ $team->getMemberCount() }}</td>
                                    <td>
                                        <a href="#">
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
@endsection