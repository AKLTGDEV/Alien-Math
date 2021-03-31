@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/profile.css') }}">

<script>
    jQuery(document).ready(function() {
        $("title").text(`{{ $user->name }} {{ "(@".$user->username.")" }} / {{ config('app.name', 'Crowdoubt') }}`);

        @if($newuser)
        $("#NewUserModal").modal('show')
        @endif

        $("#updatebtn").click(function(e) {
            $("#updateform").submit();
        })
    })
</script>

@include('logic.feeditem')

<?php

use App\users;
use Illuminate\Support\Facades\Auth;


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

?>

<div class="cover"></div>
<div class="container content main">
    <div class="row justify-content-center">
        <div class="col-md" id="left">
            <div class="short-holder">
                @include('profile.short2')

                <!--
                    TODO

                    <a class="card btn btn-outline-info" style="width: 100%;" href="{{ route('qbank_index') }}">
                    My Question Bank
                </a>-->
            </div>
        </div>
        <div class="col-md">
            @if ($self_flag && $classes_goals)
            <div class="row mt-4">
                <!--<div class="col font-weight-bold"> Profile Info </div>-->
            </div>
            <div class="row mt-4">
                <div class="col-lg-6">
                    <div class="card" id="upcomingTests">
                        <div class="card-header">
                            Upcoming Tests <a class="action" href="#"><i class="fas fa-bars"></i></a>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    None
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card" id="classrooms">
                        <div class="card-header">Classrooms <a class="action" href="{{ route('createclassroom') }}"><i class="fas fa-plus"></i></a></div>
                        <ul class="list-group">
                            <li class="list-group-item">
                                @if (count($classrooms_list) > 0)

                                <?php foreach ($classrooms_list as $class) { ?>
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-title">
                                                <div class="h5">
                                                    <a href="{{ route('viewclassroom', [$class->id]) }}">
                                                        {{ $class->name }}
                                                        @if ($class->author == $me->id)
                                                        <div style="text-decoration:none;" class="py-1 badge badge-pill badge-primary">admin</div>
                                                        @else
                                                        <div style="text-decoration:none;" class="py-1 badge badge-pill badge-primary">member</div>
                                                        @endif
                                                    </a>
                                                </div>
                                            </div>
                                            {{ $class->users }} members
                                        </div>
                                    </div>
                                <?php } ?>
                                @else
                                None
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            @if ($notifs)
            <div class="row notifs-holder">
                <div class="col">
                    <div class="alert bg-primary text-white">
                        <button class="close" data-dismiss="alert"><span>&times;</span></button>
                        <div class="row">
                            <div class="col-lg">
                                <div class="row">
                                    <div class="col">2015 Innovation Conference</div>
                                </div>
                                <div class="row my-2">
                                    <div class="col small text-white">Interesting speakers, delicious food, do not miss this event!</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @endif
            <div class="row">
                <div class="col">
                    <div class="card" id="recentActivity">
                        <!--<div class="card-header">Recent Activity</div>-->
                        <ul class="list-group">
                            <?php
                            $story_id = 1;
                            //foreach ($posts as $post) {
                            //dd($items);
                            foreach ($items as $item) {
                                $type = 0;
                                if ($item['type'] == 1 || $item['type'] == 3) {
                                    $post = $item;
                                    $corr = $post['correctopt'];
                                    $given = $post['givenopt'];
                                    $tags = json_decode($post['tags'], true);
                                    $type = $post['type'];
                                } else if ($item['type'] == 2 || $item['type'] == 4) {
                                    $ws = $item;
                                    $tags = json_decode($ws['tags'], true);
                                    $type = $ws['type'];
                                }

                                ?>
                                <li class="list-group-item">
                                    @if($type == 1 || $type == 3)
                                    @if($type == 1)
                                    {{ $user->name }} posted a Question
                                    @else
                                    {{ $user->name }} answered a Question
                                    @endif
                                    <b> &#183; </b> {{ $post['samay'] }}
                                    @include('includes.feeditem')
                                    @else
                                    @if($type == 2)
                                    {{ $user->name }} posted a Worksheet
                                    @else
                                    {{ $user->name }} answered a Worksheet
                                    @endif
                                    <b> &#183; </b> {{ $ws['samay'] }}
                                    @include('includes.wsitem')
                                    @endif
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($newuser)

<div class="modal" id="NewUserModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="lead text-center text-secondary mb-2">
                    Welcome to CrowDoubt !
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <p class="text-info display-4">
                            Teachers
                        </p>
                        <p class="text-center">
                            Start by creating a Classroom. Invite your students and hold classes!
                        </p>
                        <a href="{{ route('createclassroom') }}" style="width: 100%" class="btn btn-sm btn-outline-primary">Take me there</a>
                    </div>
                    <div class="col-md-6">
                        <p class="text-info display-4">
                            Students
                        </p>
                        <p class="text-center">
                            Get the latest and Best Worksheets from CrowDoubt!
                        </p>
                        <a href="{{ route('explore') }}" style="width: 100%" class="btn btn-sm btn-outline-primary">Take me there</a>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

@endif

@endsection