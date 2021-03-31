@extends('layouts.app')

@section('content')

<style>
    .feed_item {
        margin-top: 2%;
    }

    .btn {
        white-space: normal !important;
        word-wrap: break-word;
    }

    .sidebar__inner {
        /*background: yellow;*/
    }
</style>

<script type="module">
    import StickySidebar from "./../thirdparty/sticky-sidebar.js";

	var a = new StickySidebar('#sidebar', {
			topSpacing: 40,
			bottomSpacing: 20,
			containerSelector: '.container',
			innerWrapperSelector: '.sidebar__inner'
		});
</script>

<div class="row" style="height: 100%;">
    @if($carousel)
    @include('includes.topcarousel')
    @endif
</div>
<div style="padding: 5px;">
    <div class="container">
        <div class="row main" id="main-content">
            <div id="content" class="feedholder col-12 col-sm-12 col-md-8 col-lg-8 col-xl-8">
                <div class="container-fluid">
                    @include('logic.feeditem')
                    <?php
                    $story_id = 1;
                    foreach ($posts as $post) {
                        $corr = $post['correctopt'];
                        $given = $post['givenopt'];
                        $tags = json_decode($post['tags'], true);
                        ?>

                        @include('includes.feeditem')

                    <?php
                        $story_id++;
                    }

                    ?>
                </div>
            </div>
            <div class="sidebar d-none d-xs-none d-sm-none d-md-block col-md-4 d-lg-block col-lg-4 d-xl-block col-xl-4">
                <div id="sidebar">
                    <div class="sidebar__inner">
                        <div class="container-fluid mt-3">
                            <div class="row">
                                <div class="col-12 mt-2">
                                    <div class="card shadow">
                                        <div class="card-header py-3">
                                            <div class="d-sm-flex justify-content-between align-items-center mb-0">
                                                <h5 class="text-secondary mb-0">Topic &#183; <span class="text-info">{{ $tag }}</span></h5>
                                                @if ($following == false)
                                                <a class="btn btn-outline-primary btn-sm d-none d-sm-inline-block" role="button" href="{{ route('namedtagfollow', [$tag]) }}">
                                                    &nbsp;Follow
                                                </a>
                                                @else
                                                <a class="btn btn-outline-secondary btn-sm d-none d-sm-inline-block" role="button" href="{{ route('namedtagunfollow', [$tag]) }}">
                                                    &nbsp;Unfollow
                                                </a>
                                                @endif
                                            </div>
                                        </div>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">
                                                <div class="row align-items-center no-gutters">
                                                    <div class="col-12 text-primary text-center lead">
                                                        {{ $nos_posts }} Posts
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="row align-items-center no-gutters">
                                                    <div class="col-12 text-primary text-center lead">
                                                        {{ $nos_worksheets }} Worksheets
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="row align-items-center no-gutters">
                                                    <div class="col-12 text-primary text-center lead">
                                                        {{ $nos_followers }} Followers
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 mt-2">
                                    <div class="card px-2 py-2">
                                        CROWDOUBT PREMIUM LINK
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 mt-2">
                                    <div class="card shadow">
                                        <div class="card-header py-3">
                                            <h6 class="text-primary font-weight-bold m-0">
                                                Trending people
                                            </h6>
                                        </div>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">
                                                <div class="row align-items-center no-gutters">
                                                    <div class="col mr-2">
                                                        <h6 class="mb-0"><strong>Mast Aadmi 1</strong></h6>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="custom-control custom-checkbox"><input class="custom-control-input" type="checkbox" id="formCheck-1"><label class="custom-control-label" for="formCheck-1"></label></div>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="row align-items-center no-gutters">
                                                    <div class="col mr-2">
                                                        <h6 class="mb-0"><strong>Mast Aadmi 2</strong></h6>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="custom-control custom-checkbox"><input class="custom-control-input" type="checkbox" id="formCheck-2"><label class="custom-control-label" for="formCheck-2"></label></div>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="row align-items-center no-gutters">
                                                    <div class="col mr-2">
                                                        <h6 class="mb-0"><strong>Mast Aadmi 3</strong></h6>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="custom-control custom-checkbox"><input class="custom-control-input" type="checkbox" id="formCheck-3"><label class="custom-control-label" for="formCheck-3"></label></div>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    TEXT
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection