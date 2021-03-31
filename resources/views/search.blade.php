@extends('layouts.app')

@section('content')

<!--
    Everything is almost the same as HOME, except 
    the fact that feeditems are replaced by search 
    results. Method of treating them remains the same.
-->

<style>
    .feed_item {
        margin-top: 2%;
        margin-bottom: 2%;
    }

    .btn {
        white-space: normal !important;
        word-wrap: break-word;
    }
</style>


<div style="padding: 5px;">
    <div class="container">
        <div class="row main" id="main-content">
            <div id="content" class="content feedholder col-12 col-sm-12 col-md-8 col-lg-8 col-xl-8">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header lead">
                            Search results for "{{ $query }}"
                        </div>
                        <div class="card-body">
                            <?php

                            use App\tags;
                            use App\users;
                            use Illuminate\Support\Facades\Auth;

                            foreach ($results as $searchitem) { ?>

                                @if ($searchitem['type'] == "POST")


                                <!-- POST ITEM-->
                                @include('logic.feeditem')
                                <?php

                                    $post = $searchitem['body'];

                                    $corr = $post['correctopt'];
                                    $given = $post['givenopt'];
                                    $tags = json_decode($post['tags'], true);
                                    ?>

                                @include('includes.feeditem')
                                <!-- END POST ITEM-->


                                @elseif ($searchitem['type'] == "WS")


                                <!-- WS ITEM-->
                                <?php
                                    $ws = $searchitem['body'];
                                    $tags = json_decode($ws['tags'], true);
                                    ?>
                                @include('includes.wsitem')
                                <!-- END WS ITEM-->


                                @elseif ($searchitem['type'] == "TAG")
                                <!-- TAG ITEM-->
                                <?php
                                    $tag = $searchitem['body'];
                                    $tag_name = $tag->name;
                                    $u_tags = json_decode(users::gettags(Auth::user()->username), true);
                                    $tag_following_flag = false;
                                    foreach ($u_tags as $tag_single) {
                                        if ($tag_single == $tag_name) {
                                            $tag_following_flag = true;
                                        }
                                    }

                                    $tag_nos_posts = count(tags::allposts($tag->name));
                                    $tag_nos_worksheets = 0;
                                    $tag_nos_followers = $tag->followers;
                                    ?>
                                @include('includes.tagitem')
                                <!-- END TAG ITEM-->


                                @elseif ($searchitem['type'] == "USER")

                                <!-- USER ITEM-->
                                <?php
                                    $user = $searchitem['body'];

                                    $name = $user->name;
                                    $username = $user->username;
                                    $bio = users::getbio($username);
                                    $tags = json_decode(users::gettags($user->username), true);
                                    $nos_Q = $user->nos_Q;
                                    $nos_A = $user->nos_A;
                                    $nos_followers = $user->nos_followers;
                                    $nos_following = $user->nos_following;
                                    $rating = $user->rating;

                                    $me = Auth::user();

                                    if ($me->username == $username) {
                                        $self_flag = true;
                                    } else {
                                        $self_flag = false;
                                    }
                                    $self = $self_flag;

                                    $following_flag = false;
                                    if (!$self) {
                                        /**
                                         * Not the current user's profile. Check if the 
                                         * current user follows this user or not.
                                         */

                                        $self_following = json_decode($me->following, true);
                                        if (in_array($user->id, $self_following)) {
                                            $following_flag = true;
                                        } else {
                                            $following_flag = false;
                                        }
                                    }

                                    ?>

                                <style>
                                    .pshort_card {
                                        margin-top: 5px;
                                    }
                                </style>

                                <div class="row">
                                    <div class="col-12 col-xl-10">
                                        @include('profile.short2')
                                    </div>
                                </div>
                                <!-- END USER ITEM-->

                                @endif

                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection