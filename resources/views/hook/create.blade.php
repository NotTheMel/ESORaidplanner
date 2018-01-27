@extends('layouts.app')

@section('content')
    <script type="text/javascript">
        function showfield(name) {
            if (name == '2') document.getElementById('div1').innerHTML = 'Send notification <input class="form-control" required name="call_time_diff" type="number" value="">minutes before the event starts<br><br>';
            else document.getElementById('div1').innerHTML = '';
        }
    </script>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">Create a Notification</h4>
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

                            @if ($type === 2)
                                <div class="alert alert-warning">
                                    Make sure to add @esorpnotificationbot to your group in order for Telegram
                                    notifications to work.
                                </div>
                            @endif

                            {{ Form::open(array('url' => 'hooks/create/' . $type)) }}

                            Notification name (just for your
                            reference):{!! Form::text('name', '', ['class' => 'form-control', 'required']) !!}<br>

                            This message is
                            for:{!! Form::select('owner', $guilds, 0, array('class' => 'form-control')) !!}<br>

                            @if ($type === 1 || $type === 3)
                                Hook url:{!! Form::text('url', '', ['class' => 'form-control', 'required']) !!}<br>
                            @elseif ($type === 2)
                                Chat ID
                                (Telegram):{!! Form::text('chat_id', '', ['class' => 'form-control', 'required']) !!}
                                <br>
                            @endif
                            Notification
                            Type:{!! Form::select('call_type', [1 => 'Send notification when a new event is created', 2 => 'Send notification X minutes before the event starts'], 1, ['class' => 'form-control', 'onchange' => 'showfield(this.options[this.selectedIndex].value)']) !!}
                            <br>
                            <div id="div1"></div>
                            Only send this message if the event has less signups than (leave blank or 0 to always send
                            the message):
                            {!! Form::number('if_less_signups', '', array('class' => 'form-control')) !!}<br><br>
                            Message to be sent:<br>
                            In the message you can use shortcodes that will be replaced by the actual values. See the
                            list of shortcodes below.<br>
                            {!! Form::textarea('message', '', ['class' => 'form-control', 'required']) !!}<br>

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