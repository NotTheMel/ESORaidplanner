@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">
                            <div class="pull-right">
                                <a href="{{ '/profile/menu' }}">
                                    <button type="button" class="btn">Back to user menu</button>
                                </a>
                            </div>
                            <h4 class="title">Your avatar</h4>
                        </div>
                        <div class="content">
                            <p align="center"><img src="/storage/avatars/{{ Auth::user()->avatar }}"
                                                   style="max-width: 15%"><br>
                                {{ Auth::user()->name }}</p>
                        </div>

                        <div class="header">
                            <h4 class="title">Upload a custom avatar</h4>
                        </div>
                        <div class="content">
                            @if( Auth::user()->membership_level > 0)
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                {{ Form::open(array('url' => '/profile/edit/avatar/upload', 'enctype' => 'multipart/form-data')) }}
                                Image:{!! Form::file('avatar', array()) !!}<br>
                                {!! Form::submit('Upload Avatar', ['class' => 'btn']) !!}<br>

                                {{ Form::close() }}
                            @else
                                Custom avatars are a perk for supporters. If you would like a custom avatar, please
                                consider becoming a supporter on <a href="https://patreon.com/woeler" target="_blank">Patreon</a>
                                . The $1 bronze membership already unlocks custom avatars!
                            @endif
                        </div>

                        <div class="header">
                            <h4 class="title">Avatar library</h4>
                        </div>
                        <div class="content">

                            @if (!empty($error))
                                <p class="text-warning">{{ $error }}</p><br>
                            @endif

                            {{ Form::open(array('url' => '/profile/edit/avatar')) }}
                            <?php $i = 0; ?>
                            <table class="table">
                                <tbody>
                                @foreach ($avatars as $avatar)
                                    @if ($i === 0)
                                        <tr align="center">
                                            @endif
                                            @if(Auth::user()->avatar === $avatar->file)
                                                <td><img src="/storage/avatars/{{ $avatar->file }}"><br>
                                                    {{ Form::radio('avatar', $avatar->file, true) }} {{ $avatar->name }}
                                                </td>
                                                <?php $i++; ?>
                                            @else
                                                <td><img src="/storage/avatars/{{ $avatar->file }}"><br>
                                                    {{ Form::radio('avatar', $avatar->file) }} {{ $avatar->name }}</td>
                                                <?php $i++; ?>
                                            @endif
                                            @if ($i === 4)
                                                <?php $i = 0; ?>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>

                            {!! Form::submit('Save Avatar', ['class' => 'btn']) !!}<br>

                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection