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

$feeditem_ajax_url = route('getfeed_profile', [$username]);


?>

@include('profile.logic.getfeed')

<div class="cover"></div>
<div class="container content main">
    <div class="row justify-content-center">
        <div class="col-md" id="left">
            <div class="short-holder">
                @include('profile.short2')
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
                            Search Posted Questions
                        </div>
                        <div class="card-body">
                            <!--<ul class="list-group">
                                @if (count($TS) > 0)
                                <?php foreach ($TS as $test) { ?>
                                    <li class="list-group-item">
                                        <div class="h5">
                                            <a href="{{ route('TSindex', [$test->encname]) }}">
                                                {{ $test->name }}
                                                @if ($test->author == $me->id)
                                                <div style="text-decoration:none;" class="py-1 badge badge-pill badge-primary">author</div>
                                                @else
                                                <div style="text-decoration:none;" class="py-1 badge badge-pill badge-primary">student</div>
                                                @endif
                                            </a>
                                        </div>
                                    </li>
                                <?php } ?>
                                @else
                                <li class="list-group-item">
                                    None
                                </li>
                                @endif
                            </ul>-->

                            <ul class="list-group">
                                <form action="{{ route('q.search') }}">
                                    <div class="input-group">
                                        <input name="q" type="text" class="form-control" placeholder="Search Uploaded Questions">
                                        <div class="input-group-append">
                                            <button class="btn btn-secondary" type="submit">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
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
            <div class="row mt-1">
                <div class="col">
                    <div class="card">
                        <div class="card-body" id="feed_holder">

                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12" id="req">
                    <div style="width: 100%" class="mt-2 btn btn-outline-primary" id="req-text">
                        Load More
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