@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">

                <div class="col-md-12">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">{{ $guild->name }} is inactive</h4>
                            <p class="category">{{ $guild->platform() }}
                                - {{ $guild->megaserver() }}</p>
                        </div>
                        <div class="content">
                            <div class="alert alert-danger">
                                This guild has been deactivated because of inactivity.
                            </div>
                            @if($guild->isAdmin(Auth::user()))
                                <a href="{{ route('guildActivate', ['slug' => $guild->slug]) }}">
                                    <button class="btn btn-success">Reactivate</button>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
