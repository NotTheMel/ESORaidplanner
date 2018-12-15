@if($guild->isAdmin(Auth::user()))
    {{ Form::open(array('url' => route('eventSetSignupStatus', ['slug' => $guild->slug, 'event_id' => $event->id]))) }}
@endif

@foreach(\App\Utility\Roles::ROLES as $role_id => $role)
    <div class="content table-responsive table-full-width" style="z-index: 9999">
        <br>
        <h3 align="center">{{ $role  }}</h3>
        <table class="table  table-striped">
            <thead>
            @if ($guild->isAdmin(Auth::user()))
                <th width="20%">Player</th>
                <th width="15%">Class</th>
                <th width="20%">Role</th>
                <th width="20%">Sets</th>
                <th width="5%"></th>
                <th width="20%" colspan="3" class="text-center">Actions</th>
            @else
                <th width="25%">Player</th>
                <th width="20%">Class</th>
                <th width="25%">Role</th>
                <th width="25%">Sets</th>
                <th width="5%"></th>
            @endif
            </thead>
            <tbody>
            @foreach ($event->signupsByRole($role_id) as $signup)
                @if ($signup->status === 1)
                    <tr style="background-color: rgba(50, 205, 50, 0.5);">
                @elseif ($signup->status === 2)
                    <tr style="background-color: rgba(255, 255, 0, 0.5);">
                @else
                    <tr>
                        @endif
                        <td>{{ $signup->user->name }}</td>
                        <td><img width="30px" src="/img/classes/{{ $signup->classIcon() }}"
                                 alt="{{ $signup->class() }}"
                                 title="{{ $signup->class() }}"></td>
                        <td><img width="30px" src="/img/roles/{{ $signup->roleIcon() }}"
                                 alt="{{ $signup->role() }}"
                                 title="{{ $signup->role() }}"></td>
                        <td>{!! implode(', ', $signup->getSets()) !!}</td>
                        <td><i class="fa fa-clock-o"
                               title="Signup time: {{ $signup->getUserHumanReadableDate() }}"></td>
                        @if ($guild->isAdmin(Auth::user()))
                            <td align="center">
                                {{ Form::checkbox($signup->id, $signup->id) }}
                            </td>
                        @endif
                    </tr>
                    @endforeach
            </tbody>
        </table>
    </div>
@endforeach

@if($guild->isAdmin(Auth::user()))
    <div class="content">
        <div class="pull-right">
            {!! Form::submit('Confirm selected', ['class' => 'btn btn-success', 'name' => 'action', 'value' => 'confirm']) !!}
            {!! Form::submit('Backup selected', ['class' => 'btn btn-warning', 'name' => 'action', 'value' => 'backup']) !!}
            {!! Form::submit('Reset selected', ['class' => 'btn btn-info', 'name' => 'action', 'value' => 'reset']) !!}
            {!! Form::submit('Delete selected', ['class' => 'btn btn-danger', 'name' => 'action', 'value' => 'delete']) !!}
        </div>
    </div>

    {{ Form::close() }}
    <br><br>
@endif