@if($post['itemT'] == "post")
<div class="card feed_item">
    @if ($post['image'] != null)
    <div class="view overlay">
        <img class="card-img-top" src="{{ config('app.url') }}/posts/view/{{ $post['pid'] }}/image" alt="Card image cap">
        <a href="#!">
            <div class="mask rgba-white-slight"></div>
        </a>
    </div>
    @endif

    <div class="card-body">
        <div class="card-title">
            <span>
                <h4>{{ $post['title'] }}</h4>
                <span>
                    <a class="avatar mx-auto white" href="{{ config('app.url') }}/u/<?php echo $post['username'] ?>">
                        <img style="height: 40px;" src="{{ $post['profilepic'] }}" alt="avatar mx-auto white" class="rounded-circle img-fluid">
                        <span>
                            {{ $post['name'] }}
                        </span>
                    </a>
                </span>
            </span>
        </div>
        <p class="card-text">
            <?php echo $post['body']; ?>
        </p>
        <div class="fb-like" data-share="true" data-width="450" data-show-faces="true">
        </div>
        <div class="section-fluid">
            <div class="post-tags-list">
                <?php foreach ($tags as $tag) { ?>
                    <a style="text-decoration:none" class="badge badge-secondary" href="{{ config('app.url') }}/tags/{{ $tag }}">{{ $tag }}</a>
                <?php } ?>
            </div>
        </div>
        <div class="section-fluid mt-1">
            @include('includes.opts')
        </div>
        <div class="answers">
            <span class="badge badge-pill badge-success"><span id="right-<?php echo $post['pid'] ?>"> <?php echo $post['right'] ?></span> </span>
            <span class="badge badge-pill badge-danger"><span id="wrong-<?php echo $post['pid'] ?>"> <?php echo $post['wrong'] ?></span> </span>
        </div>
    </div>
</div>
@endif