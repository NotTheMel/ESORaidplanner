<div class="card">
    <div class="header">
        <h4 class="title">Comments</h4>
    </div>
    <div class="content">
        @foreach($event->comments() as $comment)
            <div class="row">
                <div class="col-sm-2">
                    <div class="thumbnail">
                        <img class="img-responsive user-photo"
                             src="{{ asset('/storage/avatars/' . $comment->user->avatar) }}">
                    </div><!-- /thumbnail -->
                </div><!-- /col-sm-1 -->

                <div class="col-sm-10">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <strong>{{ $comment->user->name }}</strong> <span
                                    class="text-muted">{{ $comment->getUserHumanReadableDate() }}</span>
                            @if ($comment->user->id === Auth::id() || $guild->isAdmin(Auth::user()))
                                <a href="{{ route('eventDeleteComment', ['slug' => $guild->slug, 'event_id' => $event->id, 'comment_id' => $comment->id]) }}">Remove</a>
                            @endif
                        </div>
                        <div class="panel-body">
                            {{  $comment->text }}
                        </div><!-- /panel-body -->
                    </div><!-- /panel panel-default -->
                </div>
            </div>
        @endforeach

        {{ Form::open(array('url' => route('eventAddComment', ['slug' => $guild->slug, 'event_id' => $event->id]))) }}
        {!! Form::open([]) !!}

        {!! Form::textarea('text', '', ['class' => 'form-control', 'required']) !!}<br>

        {!! Form::submit('Comment', ['class' => 'btn btn-info']) !!}
    </div>
</div>