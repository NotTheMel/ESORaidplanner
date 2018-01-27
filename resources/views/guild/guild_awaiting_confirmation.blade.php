@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">

                <div class="col-md-12">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">{{ $guild->name }}</h4>
                            <p class="category">{{ $guild->getPlatform() }} - {{ $guild->getMegaserver() }}</p>
                        </div>
                        <div class="content">
                            You are not a member of {{ $guild->name }}.<br>
                            You have requested membership to this guild. This request is pending.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
