@extends('layouts.app')

@section('content')
    <div class="content">

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <h4 class="title">Connect to Patreon</h4>
                    </div>
                    <div class="content">
                        <h4 align="center">Your Patreon account is now connected</h4>
                        <p align="center">
                            You now have {{ Auth::user()->getMembershipLevel() }} membership!<br>
                            <img src="{{ asset('img/patreon.png') }}" alt="patreon" width="170px"><br>
                            Thank you for being a patron! Your patreon benefits for ESO Raidplanner have now
                            automatically been set!<br</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection