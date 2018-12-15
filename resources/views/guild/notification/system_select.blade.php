@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">Select the system you want to send the notification to</h4>
                            <p class="category"></p>
                        </div>
                        <div class="content table-responsive table-full-width">
                            <table class="table  table-striped">
                                <thead>
                                <th>Name</th>
                                </thead>
                                <tbody>
                                @foreach (\App\Notification\Configuration::SYSTEM_TYPES as $system)
                                    <tr>
                                        <td>{{ $system::NAME }}</td>
                                        <td>
                                            <a href="{{ route('notificationCreateView', ['slug' => $guild->slug, 'message_type' => $message_type, 'system_type' => $system::SYSTEM_ID]) }}">
                                                <button class="btn btn-primary" type="button">Select</button>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection
