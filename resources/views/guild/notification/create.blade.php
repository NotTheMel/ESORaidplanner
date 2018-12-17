@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">
                                Give me all the details</h4>
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

                            @if ($system_type === \App\Notification\System\TelegramSystem::SYSTEM_ID)
                                <div class="alert alert-warning">
                                    Make sure to add @esorpnotificationbot to your group in order for Telegram
                                    notifications to work.
                                </div>
                            @endif

                            {{ Form::open(array('url' => route('notificationCreate', ['slug' => $guild->slug, 'message_type' => $message_type, 'system_type' => $system_type]))) }}

                            Notification name (just for your
                            reference):{!! Form::text('name', '', ['class' => 'form-control', 'required']) !!}<br>

                            @if ($system_type === \App\Notification\System\DiscordSystem::SYSTEM_ID || $system_type === \App\Notification\System\SlackSystem::SYSTEM_ID)
                                Webhook url:{!! Form::text('url', '', ['class' => 'form-control', 'required']) !!}<br>
                            @elseif ($system_type === \App\Notification\System\TelegramSystem::SYSTEM_ID)
                                Chat ID
                                (Telegram):{!! Form::text('chat_id', '', ['class' => 'form-control', 'required']) !!}
                                <br>
                            @endif
                            @if(\in_array($message_type, \App\Notification\Configuration::RELATIVE_TIME_BASED_MESSAGES))
                                How many minutes before the event should this notification be sent?:
                                {!! Form::number('call_time_diff', '', array('class' => 'form-control')) !!}<br>
                            @endif
                            @if(\in_array($message_type, \App\Notification\Configuration::SIGNUP_BASED_MESSAGES))
                                Only send this message if the event has less signups than (leave blank or 0 to always
                                send
                                the message):
                                {!! Form::number('if_less_signups', '', array('class' => 'form-control')) !!}<br>
                                Only send this message if the event has this many signups or more (leave blank or 0 to
                                always
                                send
                                the message):
                                {!! Form::number('if_more_signups', '', array('class' => 'form-control')) !!}<br>
                            @endif

                            @if(\in_array($message_type, \App\Notification\Configuration::TAG_BASED_MESSAGES))
                                Notification tags (separate each tag with a comma, using tags will make sure this
                                notification is only sent for events that contain a matching tag):
                                {!! Form::text('tags', '', ['class' => 'form-control']) !!}<br>
                            @endif
                            @if(in_array($message_type, \App\Notification\Configuration::DAILY_MESSAGES))
                                <div class="row">
                                    <div class="col-md-12">
                                        Set the time you want this notification to be sent at. It will be sent daily
                                        based on your configured timezone ({{ Auth::user()->timezone }}).
                                    </div>
                                    @if (Auth::user()->clock === 12)
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                Hour:{!! Form::number('hour', '', array('min' => '1', 'max' => '12', 'class' => 'form-control')) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                Minute:{!! Form::number('minute', '', array('min' => '0', 'max' => '59', 'class' => 'form-control')) !!}
                                                <br>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                Meridiem:{!! Form::select('meridiem', ['am' => 'am', 'pm' => 'pm'], 'am', array('class' => 'form-control')) !!}
                                                <br>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                Hour:{!! Form::number('hour', '', array('min' => '0', 'max' => '23', 'class' => 'form-control')) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                Minute:{!! Form::number('minute', '', array('min' => '0', 'max' => '59', 'class' => 'form-control')) !!}
                                                <br>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @if(in_array($message_type, \App\Notification\Configuration::TIMEZONE_BASED_MESSAGES))
                                Some guilds span over multiple timezones. This notification includes event start dates
                                and times.

                                {!! Form::select('timezones[]', \App\Utility\UserDateHandler::timeZones(), null, array('class' => 'chosen-select form-control', 'multiple')) !!}
                            @endif
                            <br>
                            @if(in_array($message_type, \App\Notification\Configuration::HAS_CUSTOM_TEXT_MESSAGE))
                                Message to be sent:<br>
                                In the message you can use shortcodes that will be replaced by the actual values. See
                                the
                                list of shortcodes below.<br>
                                {!! Form::textarea('message', '', ['class' => 'form-control', 'required']) !!}<br>
                            @endif

                            @if ($system_type === \App\Notification\System\DiscordSystem::SYSTEM_ID)
                                Use fancy Discord messages: {!! Form::checkbox('has_embeds', 1, true); !!}<br>
                            @endif

                            {!! Form::submit('Create Notification', ['class' => 'btn btn-info']) !!}<br>

                            {{ Form::close() }}

                            @if(in_array($message_type, \App\Notification\Configuration::HAS_CUSTOM_TEXT_MESSAGE))
                                <div class="content table-responsive table-full-width">
                                    <table class="table  table-striped">
                                        <thead>
                                        <th width="50%">Shortcode</th>
                                        <th width="50%">Description</th>
                                        </thead>
                                        <tbody>
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
                                        <tr>
                                            <td>{EVENT_NUM_SIGNUPS}</td>
                                            <td>Will be replaced by the total number of members that have signed up for
                                                the
                                                event.
                                            </td>
                                        </tr>
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
                                        </tbody>
                                    </table>
                                </div>
                            @endif
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

                            @if ($system_type === \App\Notification\System\DiscordSystem::SYSTEM_ID)
                                <b>Hook url:</b> This is an url you can obtain in Discord. Open Discord and go to the
                                channel you would like to post the notification to. Click the cog-wheel right of the
                                channel name. In the menu that pops up select the 'Webhooks' option on the left. Create
                                a new one. Give it a name (like 'esoraidplanner', though whatever you name it is
                                irrelevant). There should be a very long url at the bottom. Copy this url and use it in
                                this field. For more information click <a
                                        href="https://support.discordapp.com/hc/en-us/articles/228383668-Intro-to-Webhooks"
                                        target="_blank">here</a>.
                                <hr>
                            @elseif ($system_type === \App\Notification\System\TelegramSystem::SYSTEM_ID)
                                <div class="alert alert-warning">
                                    Make sure to add @esorpnotificationbot to your group in order for Telegram
                                    notifications to work.
                                </div>
                                <b>Chat ID:</b> This is the chat id the Telegram bot should post to. If this is a group
                                you will need to get the chat id of that group. This is a little bit of a hassle. You
                                need to log in to <a href="https://web.telegram.org/" target="_blank">Telegram Web</a>.
                                <hr>
                            @endif
                            @if(\in_array($message_type, \App\Notification\Configuration::RELATIVE_TIME_BASED_MESSAGES))
                                <b>How many minutes before the event should this notification be sent?:</b> Here is
                                where you specify how many minutes before the event starts this notification should be
                                sent. If you for instance set this to 10, the notification will be sent 10 minutes
                                before the event starts.
                                <hr>
                            @endif
                            @if(\in_array($message_type, \App\Notification\Configuration::SIGNUP_BASED_MESSAGES))
                                <b>Only send this message if the event has less signups than (leave blank or 0 to always
                                    send
                                    the message):</b> Here you can specify if this notification should only be sent if
                                there are less than an X amount of people signed up. This is handy if you want to send
                                signup reminders, but only if the event isn't full. Leaving this blank or setting it to
                                0 will disable this, and makes sure the notification is always sent, regardless of the
                                number of people signed up.
                                <hr>
                            @endif
                            @if(\in_array($message_type, \App\Notification\Configuration::TAG_BASED_MESSAGES))
                                <b>Notification tags:</b> Tags can be used to make sure certain notifications only
                                trigger for certain events. You can fill in a comma separated list of tags here. If one
                                of the tags matches one of the tags of an event, the notification will be sent.
                                Otherwise it will be ignored. If you do not want to use tags simply leave this field
                                blank.
                                <hr>
                            @endif
                            @if(in_array($message_type, \App\Notification\Configuration::HAS_CUSTOM_TEXT_MESSAGE))
                                <b>Message to be sent:</b> Here you can type the message that should be sent by your
                                notification. Make sure to have a look at the bottom of the page. There are certain
                                shortcodes you can use in order to get values like the event name or the number of
                                people
                                signed up into the notification dynamically.
                                <hr>
                            @endif
                            @if ($system_type === \App\Notification\System\DiscordSystem::SYSTEM_ID)
                                <b>Use fancy Discord messages:</b> Having this checked will send the messages to Discord
                                in a fancy way (see example <a
                                        href="#">here</a>). Unchecking this option will send the message to Discord in
                                plain text.
                                <hr>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection