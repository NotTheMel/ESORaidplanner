@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <h4 class="title">Settings for {{ Auth::user()->name }}</h4>
                    </div>
                    <div class="content">
                        <ul>
                            <li><a href="/profile/edit/avatar">Avatar</a></li>
                            <li><a href="/profile/accountsettings">Account Settings</a></li>
                            <li><a href="/profile/profilesettings">Public Profile</a></li>
                            <li><a href="/profile/characters">Character Presets</a></li>
                            <li><a href="/profile/membership">Membership (Patreon)</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection