@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">

                <div class="col-md-12">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">{{ $guild->name }}</h4>
                            <p class="category">{{ $guild->platform() }} - {{ $guild->megaserver() }}</p>
                        </div>
                        <div class="content">
                            You are not a member of {{ $guild->name }}.<br>
                            You have requested membership to this guild. This request is pending.<br>
                            <a href="{{ '/g/' . $guild->slug . '/member/leave' }}">
                                <button type="button" class="btn btn-danger">Cancel request</button>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
