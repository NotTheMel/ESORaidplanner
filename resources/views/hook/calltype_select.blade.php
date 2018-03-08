@extends('layouts.app')

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-9">
                <div class="card">
                    <div class="header">
                        <h4 class="title">What kind of notification would you like to create?</h4>
                    </div>
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <a href="/hooks/typeselect/{{ \App\Singleton\HookTypes::ON_TIME }}">
                                <h5>Create an event reminder notification</h5>
                            </a>
                        </div>
                        <div class="col-md-4 text-center">
                            <a href="/hooks/typeselect/{{ \App\Singleton\HookTypes::ON_EVENT_CREATE }}">
                                <h5>Create an event creation notification</h5>
                            </a>
                        </div>
                        <div class="col-md-4 text-center">
                            <a href="/hooks/typeselect/{{ \App\Singleton\HookTypes::ON_GUIDMEMBER_APPLICATION }}">
                                <h5>Create a notification when someone applies to my guild</h5>
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