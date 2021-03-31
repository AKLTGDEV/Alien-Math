@extends('layouts.app')

@section('content')

<style>
    .sidebar__inner {
        /*background: yellow;*/
    }

    .feed_item {
        margin-top: 2%;
        margin-bottom: 2%;
    }

    .btn {
        white-space: normal !important;
        word-wrap: break-word;
    }
</style>

<div class="row" style="height: 100%;">
    <?php
        if(!isset($carousel)){
            $carousel = false;
        }
        if(!isset($tags_to_follow_flag)){
            $tags_to_follow_flag = false;
        }
        if(!isset($people_to_follow_flag)){
            $people_to_follow_flag = false;
        }
    ?>
    @if($carousel)
    @include('includes.topcarousel')
    @endif
</div>


<script type="module">
    import StickySidebar from "./thirdparty/sticky-sidebar.js";

	var a = new StickySidebar('#sidebar', {
			topSpacing: 40,
			//bottomSpacing: 20,
			containerSelector: '#main-content',
			innerWrapperSelector: '.sidebar__inner'
	});

    var b = new StickySidebar('#sidebar2', {
			topSpacing: 40,
			//bottomSpacing: 20,
			containerSelector: '#main-content',
			innerWrapperSelector: '.sidebar__inner_2'
	});
</script>

<script>
    $(document).ready(function(e) {
        setTimeout(function() {
            window.dispatchEvent(new Event('resize'));
        }, 500)
    })
</script>

<?php

use App\users;
use Illuminate\Support\Facades\Auth;

$feeditem_ajax_url = route('reqfeed');

$user = Auth::user();
$self = $user;

$name = $user->name;
$username = $user->username;
$bio = users::getbio($username);
$tags = json_decode(users::gettags($user->username), true);
$nos_Q = $user->nos_Q;
$nos_A = $user->nos_A;
$nos_followers = $user->nos_followers;
$nos_following = $user->nos_following;
$rating = $user->rating;
$self_flag = true;
?>

@include('logic.feeditem-ajax')

<div style="padding-top: 5px;">
    <div class="container-fluid">
        <div class="row main" id="main-content">
            <div class="sidebar d-none d-xs-none d-sm-none d-md-block col-md-3 d-lg-block col-lg-3 d-xl-block col-xl-3">
                <div id="sidebar2">
                    <div class="sidebar__inner_2">
                        <div class="container-fluid mt-4 mb-3">
                            @include('profile.short2')
                        </div>
                    </div>
                </div>
            </div>
            <div id="content" class="content feedholder col-sm-12 col-md-6 col-lg-6 col-xl-6">
                <div id="posts_holder">
                    <!--<div class="card px-3 mt-1">
                        <script async src="https://ndroip.com/na/waWQiOjEwNzE0NDcsInNpZCI6MTA3ODIwMywid2lkIjoxNTgzNTQsInNyYyI6Mn0=eyJ.js"></script>
                    </div>-->
                </div>

                <div class="row">
                    <div class="col-12" id="req">
                        <div style="width: 100%" class="mt-2 btn btn-outline-primary" id="req-text">
                            Load Posts
                        </div>
                    </div>
                </div>
            </div>
            <div class="sidebar d-none d-xs-none d-sm-none d-md-block col-md-3 d-lg-block col-lg-3 d-xl-block col-xl-3">
                <div id="sidebar">
                    <div class="sidebar__inner">
                        <div class="container-fluid mt-3 mb-3">

                            @if ($tags_to_follow_flag)
                            <div class="row">
                                <div class="col-12 mt-2">
                                    <div class="card">
                                        <div class="card-header py-3">
                                            <h6 class="text-primary font-weight-bold m-0">
                                                Trending Topics
                                            </h6>
                                        </div>
                                        <ul class="list-group list-group-flush">

                                            <?php foreach ($tags_to_follow as $ttf_name) { ?>
                                                <li class="list-group-item">
                                                    <div class="row align-items-center no-gutters">
                                                        <div class="col mr-2">
                                                            <h6 class="mb-0">
                                                                <strong>
                                                                    <a href="{{ route('namedtag', [$ttf_name]) }}">
                                                                        {{ $ttf_name }}
                                                                    </a>
                                                                </strong>
                                                            </h6>
                                                        </div>
                                                        <div class="col-auto">
                                                            <a class="btn btn-outline-primary btn-sm d-none d-sm-inline-block" role="button" href="{{ route('namedtagfollow', [$ttf_name]) }}">
                                                                &nbsp;Follow
                                                            </a>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php } ?>

                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if ($people_to_follow_flag)
                            <div class="row">
                                <div class="col-12 mt-2">
                                    <div class="card">
                                        <div class="card-header py-3">
                                            <h6 class="text-primary font-weight-bold m-0">
                                                People To Follow
                                            </h6>
                                        </div>
                                        <ul class="list-group list-group-flush">

                                            <?php foreach ($people_to_follow as $ptf_uname) { ?>
                                                <li class="list-group-item">
                                                    <div class="row align-items-center no-gutters">
                                                        <div class="col mr-2">
                                                            <h6 class="mb-0">
                                                                <strong>
                                                                    <a href="{{ route('namedprofile', [$ptf_uname]) }}">
                                                                        {{ "@".$ptf_uname }}
                                                                    </a>
                                                                </strong>
                                                            </h6>
                                                        </div>
                                                        <div class="col-auto">
                                                            <a class="btn btn-outline-primary btn-sm d-none d-sm-inline-block" role="button" href="{{ route('userfollow', [$ptf_uname]) }}">
                                                                &nbsp;Follow
                                                            </a>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php } ?>

                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection