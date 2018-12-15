@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-9">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">Guild list</h4>
                        </div>
                        <div class="col-md-4 pull-right">
                            <input type="text" id="guild-searchbar" onkeyup="tableSearch()"
                                   placeholder="Search for guilds..." class="form-control">
                        </div>
                        <div class="content table-responsive table-full-width">
                            <table id="guild-table" class="table  table-striped">
                                <thead>
                                <th>Guild name</th>
                                <th>Platform</th>
                                <th>Megaserver</th>
                                </thead>
                                <tbody>
                                @foreach ($guilds as $guild)
                                    <tr>
                                        <td><a href="{{ '/g/' . $guild->slug }}">{{ $guild->name }}</a></td>
                                        <td>{{ $guild->platform() }}</td>
                                        <td>{{ $guild->megaserver() }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @include('parts.discordwidget')
            </div>
        </div>
    </div>
@endsection