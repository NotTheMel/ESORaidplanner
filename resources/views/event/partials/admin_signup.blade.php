<h3 align="center">Signup someone else</h3>
{{ Form::open(array('url' => '/g/' . $guild->slug . '/event/'.$event->id.'/signup')) }}
<div class="row">
    <div class="col-md-12">
        {!! Form::select('user', $guild->members(), null, ['class' => 'form-control']) !!}
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        Class
        {!! Form::select('class', \App\Utility\Classes::CLASSES, $signup->class_id, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-3">
        Role
        {!! Form::select('role', \App\Utility\Roles::ROLES, $signup->role_id, array('class' => 'form-control')) !!}
    </div>
    <div class="col-md-4">
        Supportive sets<br>
        {!! Form::select('sets[]', \App\Set::query()->pluck('name', 'name'), $signup->getSets() ?? [], array('class' => 'chosen-select form-control', 'multiple')) !!}
    </div>
    <div class="col-md-2">
        {!! Form::submit('Sign up', ['class' => 'btn btn-info']) !!}
    </div>
</div>
{!! Form::close() !!}