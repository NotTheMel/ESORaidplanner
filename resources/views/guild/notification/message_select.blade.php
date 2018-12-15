@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">Select the type of notification you want to create</h4>
                            <p class="category"></p>
                        </div>
                        <div class="content table-responsive table-full-width">
                            <table class="table  table-striped">
                                <thead>
                                <th>Name</th>
                                <th>Description</th>
                                </thead>
                                <tbody>
                                @foreach (\App\Notification\Configuration::MESSAGE_TYPES as $notification)
                                    <tr>
                                        <td>{{ $notification::CONFIG['name'] }}</td>
                                        <td>{{ $notification::CONFIG['description'] }}</td>
                                        <td>
                                            <a href="{{ route('notificationSystemTypeSelectView', ['slug' => $guild->slug, 'message_type' => $notification::CALL_TYPE]) }}">
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
