@extends('layouts.app')

@section('content')
    <div class="content">

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <h4>Log in with Patreon</h4>
                    </div>
                    <div class="content">
                        <a href="https://www.patreon.com/oauth2/authorize?response_type=code&client_id=2a51ef7e47efb78dc96e47d3971a34a6b4dfaade51e68bf545bd42741115ed9d&redirect_uri={{ urlencode('https://esoraidplanner.com/patreon/login') }}">Test</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection