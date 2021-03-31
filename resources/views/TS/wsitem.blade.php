@if($ws['itemT'] == "ws")
<div class="card feed_item">
    <div class="card-body">
        <div class="card-title">
            <span>
                <a href="{{ route('TSanswer', [$TS->encname, $ws['wsname']]) }}">
                    <h4>{{ $ws['title'] }}</h4>
                </a>
                <span>
                    <a class="avatar mx-auto white" href="{{ config('app.url') }}/u/<?php echo $ws['username'] ?>">
                        <img style="height: 40px;" src="{{ $ws['profilepic'] }}" alt="avatar mx-auto white" class="rounded-circle img-fluid">
                        {{ $ws['name'] }}
                    </a>
                </span>
            </span>
        </div>
        <p class="card-text">
            <ul>
                <li>{{ $ws['nos'] }} Questions</li>
                <li>To be completed in {{ $ws['mins'] }} Minutes</li>
                <li>{{ $ws['attempts'] }} Attemptees</li>
            </ul>
        </p>
        <div class="section-fluid">
            <div class="ws-tags-list">
                <?php foreach ($tags as $tag) { ?>
                    <a style="text-decoration:none" class="badge badge-secondary" href="{{ config('app.url') }}/tags/{{ $tag }}">{{ $tag }}</a>
                <?php } ?>
            </div>
        </div>
        <div class="attempt mt-1">
            <a href="{{ route('TSanswer', [$TS->encname, $ws['wsname']]) }}" class="btn btn-sm btn-outline-info waves-effect">Attempt</a>
        </div>
    </div>
</div>
@endif