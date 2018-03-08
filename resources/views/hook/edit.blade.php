@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">Edit {{ \App\Hook\NotificationHook::getTypeName($hook->type) }} {{ \App\Singleton\HookTypes::getTypeDescription($hook->call_type) }}</h4>
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

                            @if ($hook->type === \App\Hook\NotificationHook::TYPE_TELEGRAM)
                                <div class="alert alert-warning">
                                    Make sure to add @esorpnotificationbot to your group in order for Telegram
                                    notifications to work.
                                </div>
                            @endif

                            {{ Form::open(array('url' => 'hooks/modify/' . $hook->id)) }}

                            Notification name (just for your
                            reference):{!! Form::text('name', $hook->name, ['class' => 'form-control', 'required']) !!}<br>

                            @if ($hook->type === \App\Hook\NotificationHook::TYPE_DISCORD || $hook->type === \App\Hook\NotificationHook::TYPE_SLACK)
                                Hook url:{!! Form::text('url', $hook->url, ['class' => 'form-control', 'required']) !!}<br>
                            @elseif ($hook->type === \App\Hook\NotificationHook::TYPE_TELEGRAM)
                                Chat ID
                                (Telegram):{!! Form::text('chat_id', $hook->chat_id, ['class' => 'form-control', 'required']) !!}
                                <br>
                            @endif
                            @if($hook->call_type === \App\Singleton\HookTypes::ON_TIME)
                                How many minutes before the event should this notification be sent?:
                                {!! Form::number('call_time_diff', ($hook->call_time_diff / 60), array('class' => 'form-control')) !!}<br>
                                Only send this message if the event has less signups than (leave blank or 0 to always
                                send
                                the message):
                                {!! Form::number('if_less_signups', $hook->if_less_signups, array('class' => 'form-control')) !!}<br>
                            @endif
                            @if($hook->call_type === \App\Singleton\HookTypes::ON_TIME || $hook->call_type === \App\Singleton\HookTypes::ON_EVENT_CREATE)
                                Notification tags (separate each tag with a comma, using tags will make sure this
                                notification is only sent for events that contain a matching tag):
                                {!! Form::text('tags', $hook->tags, ['class' => 'form-control']) !!}<br>
                            @endif
                            <br>
                            Message to be sent:<br>
                            In the message you can use shortcodes that will be replaced by the actual values. See the
                            list of shortcodes below.<br>
                            {!! Form::textarea('message', $hook->message, ['class' => 'form-control', 'required']) !!}<br>

                            {!! Form::submit('Create Notification', ['class' => 'btn']) !!}<br>

                            {{ Form::close() }}
                            <div class="content table-responsive table-full-width">
                                <table class="table table-hover table-striped">
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
                                        <td>{EVENT_NUM_SIGNUPS}</td>
                                        <td>Will be replaced by the total number of members that have signed up for the
                                            event.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{EVENT_URL}</td>
                                        <td>The URL to the event page on esoraidplanner.
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection