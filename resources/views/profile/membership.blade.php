@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <div class="pull-right">
                            <a href="{{ '/profile/menu' }}">
                                <button type="button" class="btn">Back to user menu</button>
                            </a>
                        </div>
                        <h4 class="title">Your membership</h4>
                    </div>
                    <div class="content">
                        <p align="center">
                            @if (Auth::user()->membership_level === 0)
                                Your account does not have any membership benefits currently. You can
                                become a member by <a href="https://patreon.com/woeler" target="_blank">supporting us
                                    via Patreon</a>. Membership will give you cosmetic benefits to show off your
                                support. If you already do so, you can log in
                                with Patreon and link your account.<br><br>

                                <a href="https://www.patreon.com/oauth2/authorize?response_type=code&client_id=2a51ef7e47efb78dc96e47d3971a34a6b4dfaade51e68bf545bd42741115ed9d&redirect_uri={{ urlencode('https://esoraidplanner.com/patreon/login') }}">Click
                                    here to log in with Patreon if you already are a patron<br>
                                    <img src="{{ asset('/img/patreon.jpg') }}" width="120px"></a>
                            @else
                                You currently have {{ Auth::user()->getMembershipLevel() }} membership. Thank you
                                for supporting ESO-Raidplanner.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection