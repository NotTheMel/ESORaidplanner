@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-9">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">Select your integration</h4>
                            <i>{{ \App\Singleton\HookTypes::getTypeDescription($call_type) }}</i>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <a href="/hooks/create/{{ $call_type }}/{{ \App\Hook\NotificationHook::TYPE_DISCORD }}">
                                <img src="{{ asset('img/integrations/discord.png') }}" alt="discord" width="100px"/><br>
                                <h4>Discord</h4>
                                </a>
                            </div>
                            <div class="col-md-4 text-center">
                                <a href="/hooks/create/{{ $call_type }}/{{ \App\Hook\NotificationHook::TYPE_TELEGRAM }}">
                                <img src="{{ asset('img/integrations/telegram.png') }}" alt="telegram" width="100px"/><br>
                                <h4>Telegram</h4>
                                </a>
                            </div>
                            <div class="col-md-4 text-center">
                                <a href="/hooks/create/{{ $call_type }}/{{ \App\Hook\NotificationHook::TYPE_SLACK }}">
                                <img src="{{ asset('img/integrations/slack.png') }}" alt="slack" width="100px"/><br>
                                <h4>Slack</h4>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                @include('parts.discordwidget')
            </div>
        </div>
    </div>
@endsection