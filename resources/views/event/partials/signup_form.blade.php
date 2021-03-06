@if(!$event->locked())
    <div class="card">
        <div class="header">
            <h4 class="title">Sign up using a preset</h4>
        </div>
        <div class="content">
            @if (Auth::user()->characters()->count() === 0)
                <br>You do not have any character presets. Did you know you can create character
                presets in your <a href="{{ route('userCharacterList') }}">user profile</a> and use these to
                sign
                up
                faster?<br><br>
            @else
                <b>Use one of your presets to sign up</b><br>
                {{ Form::open(array('url' => '/g/' . $guild->slug . '/event/'.$event->id.'/signup')) }}
                <div class="row">
                    <div class="col-md-12">
                        Character preset
                        {!! Form::select('character', Auth::user()->characters()->pluck('name', 'id'), $signup->character_id, array('class' => 'form-control')) !!}
                    </div>
                    <div class="col-md-12 text-center">
                        @if(!$event->isSignedUp(Auth::user()))
                            {!! Form::submit('Sign up', ['class' => 'btn btn-info']) !!}
                        @else
                            {!! Form::submit('Save changes', ['class' => 'btn btn-info']) !!}
                        @endif
                    </div>
                </div>
                {{ Form::close() }}
            @endif
        </div>
    </div>

    <div class="card">
        <div class="header">
            <h4 class="title">Sign up using a custom setup</h4>
        </div>
        <div class="content">
            {{ Form::open(array('url' => '/g/' . $guild->slug . '/event/'.$event->id.'/signup')) }}
            <div class="row">
                <div class="col-md-12">
                    Class
                    {!! Form::select('class', \App\Utility\Classes::CLASSES, $signup->class_id, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-12">
                    Role
                    {!! Form::select('role', \App\Utility\Roles::ROLES, $signup->role_id, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-12">
                    Supportive sets<br>
                    {!! Form::select('sets[]', \App\Set::query()->pluck('name', 'name'), $signup->getSets() ?? [], ['class' => 'chosen-select form-control', 'multiple']) !!}
                </div>
                <div class="col-md-12 text-center">
                    @if(!$event->isSignedUp(Auth::user()))
                        <br>{!! Form::submit('Sign up', ['class' => 'btn btn-info']) !!}
                    @else
                        <br>{!! Form::submit('Save changes', ['class' => 'btn btn-info']) !!}
                    @endif
                    <br>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    @if($event->isSignedUp(Auth::user()))
        <div class="card">
            <div class="header">
                <div class="pull-right">
                    <a href="{{ '/g/' . $guild->slug . '/event/' . $event->id . '/signoff'}}">
                        <button type="button" class="btn btn-danger">Sign off</button>
                    </a>
                </div>
                <h4 class="title">Sign off</h4>
            </div>
            <div class="content">
            </div>
        </div>
    @endif
@else
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-warning" role="alert">
                This event has been locked by an administrator of {{ $guild->name }}.
                Signing up, editing signups or signing off is not possible until an
                administrator unlocks this event.
            </div>
        </div>
    </div>
@endif