@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">
                                Create {{ \App\Hook\NotificationHook::getTypeName($type) }} {{ \App\Singleton\HookTypes::getTypeDescription($call_type) }}</h4>
                        </div>
                        <div class="content">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if ($type === \App\Hook\NotificationHook::TYPE_TELEGRAM)
                                <div class="alert alert-warning">
                                    Make sure to add @esorpnotificationbot to your group in order for Telegram
                                    notifications to work.
                                </div>
                            @endif

                            {{ Form::open(array('url' => 'hooks/create/' . $call_type . '/' . $type)) }}

                            Notification name (just for your
                            reference):{!! Form::text('name', '', ['class' => 'form-control', 'required']) !!}<br>

                            Guild:{!! Form::select('guild_id', $guilds, null, array('class' => 'form-control')) !!}<br>

                            @if ($type === \App\Hook\NotificationHook::TYPE_DISCORD || $type === \App\Hook\NotificationHook::TYPE_SLACK)
                                Hook url:{!! Form::text('url', '', ['class' => 'form-control', 'required']) !!}<br>
                            @elseif ($type === \App\Hook\NotificationHook::TYPE_TELEGRAM)
                                Chat ID
                                (Telegram):{!! Form::text('chat_id', '', ['class' => 'form-control', 'required']) !!}
                                <br>
                            @endif
                            @if($call_type === \App\Singleton\HookTypes::ON_TIME)
                                How many minutes before the event should this notification be sent?:
                                {!! Form::number('call_time_diff', '', array('class' => 'form-control')) !!}<br>
                                Only send this message if the event has less signups than (leave blank or 0 to always
                                send
                                the message):
                                {!! Form::number('if_less_signups', '', array('class' => 'form-control')) !!}<br>
                            @endif
                            @if($call_type === \App\Singleton\HookTypes::ON_TIME || $call_type === \App\Singleton\HookTypes::ON_EVENT_CREATE)
                                Notification tags (separate each tag with a comma, using tags will make sure this
                                notification is only sent for events that contain a matching tag):
                                {!! Form::text('tags', '', ['class' => 'form-control']) !!}<br>
                            @endif
                            <br>
                            Message to be sent:<br>
                            In the message you can use shortcodes that will be replaced by the actual values. See the
                            list of shortcodes below.<br>
                            {!! Form::textarea('message', '', ['class' => 'form-control', 'required']) !!}<br>

                            {!! Form::submit('Create Notification', ['class' => 'btn btn-info']) !!}<br>

                            {{ Form::close() }}
                            <div class="content table-responsive table-full-width">
                                <table class="table  table-striped">
                                    <thead>
                                    <th width="50%">Shortcode</th>
                                    <th width="50%">Description</th>
                                    </thead>
                                    <tbody>
                                    @if($call_type === \App\Singleton\HookTypes::ON_TIME || $call_type === \App\Singleton\HookTypes::ON_EVENT_CREATE)
                                        <tr>
                                            <td>{EVENT_NAME}</td>
                                            <td>Will be replaced by the name of the event.</td>
                                        </tr>
                                        <tr>
                                            <td>{EVENT_DESCRIPTION}</td>
                                            <td>Will be replaced by the description of the event.</td>
                                        </tr>
                                        <tr>
                                            <td>{EVENT_URL}</td>
                                            <td>The URL to the event page on esoraidplanner.
                                            </td>
                                        </tr>
                                    @endif
                                    @if($call_type === \App\Singleton\HookTypes::ON_TIME)
                                        <tr>
                                            <td>{EVENT_NUM_SIGNUPS}</td>
                                            <td>Will be replaced by the total number of members that have signed up for
                                                the
                                                event.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{CONFIRMED_SIGNUPS}</td>
                                            <td>Will be replaced by a list of all confirmed signups (name, role, class).
                                            </td>
                                        </tr>
                                    @endif
                                    @if($call_type === \App\Singleton\HookTypes::ON_GUIDMEMBER_APPLICATION)
                                        <tr>
                                            <td>{GUILD_NAME}</td>
                                            <td>Name of the guild.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{GUILD_URL}</td>
                                            <td>Link to the guild page.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{APPLICANT_NAME}</td>
                                            <td>Name of the person that applied to the guild.
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">Need help?</h4>
                        </div>
                        <div class="content">
                            <div class="alert alert-success">
                                Here you can find explanations about the different fields you can use to create the
                                notification you want.
                            </div>

                            <b>Notification name:</b> This is just a name you give this particular notification. It's
                            only purpose is so that you can later edit this notification if you so desire.
                            <hr>

                            <b>Guild:</b> Here you can select the guild for this notification. Notifications are always
                            guild specific and will only trigger for events in this specific guild.
                            <hr>

                            @if ($type === \App\Hook\NotificationHook::TYPE_DISCORD)
                                <b>Hook url:</b> This is an url you can obtain in Discord. Open Discord and go to the
                                channel you would like to post the notification to. Click the cog-wheel right of the
                                channel name. In the menu that pops up select the 'Webhooks' option on the left. Create
                                a new one. Give it a name (like 'esoraidplanner', though whatever you name it is
                                irrelevant). There should be a very long url at the bottom. Copy this url and use it in
                                this field. For more information click <a
                                        href="https://support.discordapp.com/hc/en-us/articles/228383668-Intro-to-Webhooks"
                                        target="_blank">here</a>.
                                <hr>
                            @elseif ($type === \App\Hook\NotificationHook::TYPE_TELEGRAM)
                                <div class="alert alert-warning">
                                    Make sure to add @esorpnotificationbot to your group in order for Telegram
                                    notifications to work.
                                </div>
                                <b>Chat ID:</b> This is the chat id the Telegram bot should post to. If this is a group
                                you will need to get the chat id of that group. This is a little bit of a hassle. You
                                need to log in to <a href="https://web.telegram.org/" target="_blank">Telegram Web</a>.
                                <hr>
                            @endif
                            @if($call_type === \App\Singleton\HookTypes::ON_TIME)
                                <b>How many minutes before the event should this notification be sent?:</b> Here is
                                where you specify how many minutes before the event starts this notification should be
                                sent. If you for instance set this to 10, the notification will be sent 10 minutes
                                before the event starts.
                                <hr>
                                <b>Only send this message if the event has less signups than (leave blank or 0 to always
                                    send
                                    the message):</b> Here you can specify if this notification should only be sent if
                                there are less than an X amount of people signed up. This is handy if you want to send
                                signup reminders, but only if the event isn't full. Leaving this blank or setting it to
                                0 will disable this, and makes sure the notification is always sent, regardless of the
                                number of people signed up.
                                <hr>
                            @endif
                            @if($call_type === \App\Singleton\HookTypes::ON_TIME || $call_type === \App\Singleton\HookTypes::ON_EVENT_CREATE)
                                <b>Notification tags:</b> Tags can be used to make sure certain notifications only
                                trigger for certain events. You can fill in a comma separated list of tags here. If one
                                of the tags matches one of the tags of an event, the notification will be sent.
                                Otherwise it will be ignored. If you do not want to use tags simply leave this field
                                blank.
                                <hr>
                            @endif
                            <b>Message to be sent:</b> Here you can type the message that should be sent by your
                            notification. Make sure to have a look at the bottom of the page. There are certain
                            shortcodes you can use in order to get values like the event name or the number of people
                            signed up into the notification dynamically.
                            <hr>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection