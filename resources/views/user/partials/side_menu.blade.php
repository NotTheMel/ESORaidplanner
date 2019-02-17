<div class="col-md-3">
    <div class="card">
        <div class="header">
            <h4 class="title">Settings for {{ Auth::user()->name }}</h4>
        </div>
        <div class="content">
            <ul>
                <li><a href="/user/avatar">Avatar</a></li>
                <li><a href="/user/account-settings">Account Settings</a></li>
                <li><a href="/user/profile-settings">Public Profile</a></li>
                <li><a href="/user/characters">Character Presets</a></li>
                <li><a href="{{ route('userGuildsView') }}">Guilds</a></li>
                {{--<li><a href="/user/membership">Membership (Patreon)</a></li>--}}
                <li><a href="/user/ical">Personal Calendar (iCal)</a></li>
            </ul>
        </div>
    </div>
</div>
