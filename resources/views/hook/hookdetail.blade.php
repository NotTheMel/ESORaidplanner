@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">Create a Hook</h4>
                        </div>
                        <div class="content">
                            @if ($hook->type === 2)
                                <div class="alert alert-warning">
                                    Make sure to add @esorpnotificationbot to your group in order for Telegram
                                    notifications to work.
                                </div>
                            @endif
                            {{ Form::open(array('url' => 'hooks/modify/'.$hook->type.'/'.$hook->id)) }}

                            Notification name (just for your
                            reference):{!! Form::text('name', $hook->name, ['class' => 'form-control', 'required']) !!}
                            <br>

                            @if ($hook->type === 1 || $hook->type === 3)
                                Hook url:{!! Form::text('url', $hook->url, ['class' => 'form-control', 'required']) !!}
                                <br>
                            @elseif ($hook->type === 2)
                                Chat ID
                                (Telegram):{!! Form::text('chat_id', $hook->chat_id, ['class' => 'form-control', 'required']) !!}
                                <br>
                            @endif

                            @if(!empty($hook->call_time_diff))
                                Send
                                notification {!! Form::number('call_time_diff', ($hook->call_time_diff / 60), ['class' => 'form-control', 'required']) !!}
                                minutes before the event starts<br><br>
                            @endif
                            Only send this message if the event has less signups than (leave blank or 0 to always send
                            the message):
                            {!! Form::number('if_less_signups', $hook->if_less_signups, array('class' => 'form-control')) !!}
                            <br>
                            Notification tags (separate each tag with a comma, using tags will make sure this notification is only sent for events that contain a matching tag):
                            {!! Form::text('tags', $hook->tags, ['class' => 'form-control']) !!}<br><br>
                            Message to be
                            sent:{!! Form::textarea('message', $hook->message, ['class' => 'form-control', 'required']) !!}
                            <br>

                            {!! Form::submit('Save Notification', ['class' => 'btn']) !!}<br>

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