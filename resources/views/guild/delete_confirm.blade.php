@extends('layouts.app')

@section('content')
    <div class="content">

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <div class="pull-right">
                            <a href="{{ '/g/' . $guild->slug }}">
                                <button type="button" class="btn btn-info">Back to {{ $guild->name }}</button>
                            </a>
                        </div>
                    </div>
                    <div class="content">
                        <h1 style="color: red">WARNING</h1>
                        <p>You are trying to delete the guild {{ $guild->name }}. This will delete <strong>ALL</strong>
                            data associated with this guild (events, memberlists etc). Are you sure you want to proceed?
                        </p>
                        <a href="{{ '/g/' . $guild->slug }}">
                            <button type="button" class="btn btn-success">No, I don't want to delete this guild</button>
                        </a>
                        <a href="{{ '/guild/delete/' . $guild->id . '/confirm' }}">
                            <button type="button" class="btn btn-danger">Yes, I want to delete this guild and all data
                                associated with it
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection