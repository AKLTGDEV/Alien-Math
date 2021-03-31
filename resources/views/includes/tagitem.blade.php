<div class="card shadow">
    <div class="card-header py-3">
        <div class="d-sm-flex justify-content-between align-items-center mb-0">
            <h5 class="text-secondary mb-0">Topic &#183; <span class="text-info">{{ $tag_name }}</span></h5>
            @if ($tag_following_flag == false)
            <a class="btn btn-outline-primary btn-sm d-none d-sm-inline-block" role="button" href="{{ route('namedtagfollow', [$tag_name]) }}">
                &nbsp;Follow
            </a>
            @else
            <a class="btn btn-outline-secondary btn-sm d-none d-sm-inline-block" role="button" href="{{ route('namedtagunfollow', [$tag_name]) }}">
                &nbsp;Unfollow
            </a>
            @endif
        </div>
    </div>
    <ul class="list-group list-group-flush">
        <li class="list-group-item">
            <div class="row align-items-center no-gutters">
                <div class="col-12 text-primary text-center lead">
                    {{ $tag_nos_posts }} Posts
                </div>
            </div>
        </li>
        <li class="list-group-item">
            <div class="row align-items-center no-gutters">
                <div class="col-12 text-primary text-center lead">
                    {{ $tag_nos_worksheets }} Worksheets
                </div>
            </div>
        </li>
        <li class="list-group-item">
            <div class="row align-items-center no-gutters">
                <div class="col-12 text-primary text-center lead">
                    {{ $tag_nos_followers }} Followers
                </div>
            </div>
        </li>
    </ul>
</div>