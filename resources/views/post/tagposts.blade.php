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

<?php
$feeditem_ajax_url = route('namedtaggather', [$tag]);
?>
@include('logic.feeditem-ajax')

<div style="padding-top: 5px;">
    <div class="container">
        <div class="row main" id="main-content">
            <div id="content" class="feedholder col-12 col-sm-12 col-md-8 col-lg-8 col-xl-8">
                <div class="container-fluid">
                    <div id="posts_holder">
                    </div>
                    <div class="row">
                        <div class="col-12" id="req">
                            <div style="width: 100%" class="mt-2 btn btn-outline-primary" id="req-text">
                                Load Posts
                            </div>
                        </div>
                    </div>
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
                                        <div class="card-text text-center mt-1">
                                            <ul>
                                                <li>{{ $nos_posts }} Posts</li>
                                                <li>{{ $nos_followers }} Followers</li>
                                            </ul>
                                        </div>
                                    </div>
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