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
                        <h4 class="title">Logs of {{ $guild->name }}</h4>
                        <p class="category"></p>
                    </div>
                    <div class="content table-responsive table-full-width">
                        <table class="table  table-striped">
                            <thead>
                            <th>Date</th>
                            <th>Log</th>
                            </thead>
                            <tbody>
                            @foreach ($logs as $log)
                                <tr>
                                    <td>{{ $log->getUserHumanReadableDate() }}</td>
                                    <td>{!! $log->message !!}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div align="center">
                            {{ $logs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection