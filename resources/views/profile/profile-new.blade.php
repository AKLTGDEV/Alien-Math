@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/profile.css') }}">

<script src="{{ asset('thirdparty/jtr/jquery.tagsinput-revisited.js') }}"></script>
<link rel="stylesheet" href="{{ asset('thirdparty/jtr/jquery.tagsinput-revisited.css') }}" />

<script>
    jQuery(document).ready(function() {
        $("title").text(`{{ $user->name }} {{ "(@".$user->username.")" }} / {{ config('app.name', 'Crowdoubt') }}`);

        $('#topics').tagsInput();

        var tags_src = JSON.parse('<?php echo json_encode($tags_suggested); ?>');
        //console.log(tags_src)

        $("#tag_h").click(function(e) {
            $("#TagsModal").modal('show');
        });

        $("#tags-done").click(function(e) {
            //console.log("Done")
            var tag_selections = [];

            for (let k = 0; k < tags_src.length; k++) {
                //console.log($("#tag-"+k).is(':checked'));
                if ($("#tag-" + k).is(':checked')) {
                    tag_selections.push(tags_src[k]);
                }
            }

            tag_selections.forEach(T => {
                $("#topics").addTag(T)
                //console.log(T);
            });

            $("#TagsModal").modal('hide')

        })

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
                            <ul class="list-group">
                                <form action="{{ route('q.search') }}" method="get">
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" placeholder="Enter search terms" name="q">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="submit">Search</button>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="grade" class="text-muted">Select Grade</label>
                                            <select class="form-control" id="grade" name="grade">
                                                <option value="X">Any</option>
                                                <option value="P1">Primary 1</option>
                                                <option value="P2">Primary 2</option>
                                                <option value="P3">Primary 3</option>
                                                <option value="P4">Primary 4</option>
                                                <option value="P5">Primary 5</option>
                                                <option value="P6">Primary 6</option>

                                                <option value="S1">Secondary 1</option>
                                                <option value="S2">Secondary 2</option>
                                                <option value="S3">Secondary 3</option>
                                                <option value="S4">Secondary 4</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="difficulty" class="text-muted">Select difficulty</label>
                                            <select class="form-control" id="difficulty" name="difficulty">
                                                <option value="X">Any</option>
                                                <option value="1">Easy</option>
                                                <option value="2">Medium</option>
                                                <option value="3">Hard</option>
                                            </select>
                                        </div>

                                    </div>

                                    <div class="form-group" id="tag_h">
                                        <label for="topics" class="text-muted">With Topics:</label>
                                        <input class="form-control" type="text" name="topics" data-role="tagsinput" id="topics">
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

<style>
    /* Important part */
    .modal-dialog {
        overflow-y: initial !important
    }

    .modal-body {
        height: 250px;
        overflow-y: auto;
    }

    .searchable-container label.btn-default.active {
        background-color: #007ba7;
        color: #FFF
    }

    .searchable-container label.btn-default {
        width: 100%;
        border: 1px solid #efefef;


    }

    .searchable-container label .bizcontent {
        width: 100%;
    }

    .searchable-container .btn-group {
        width: 100%;
    }

    .searchable-container .btn span.glyphicon {
        opacity: 0;
    }

    .searchable-container .btn.active span.glyphicon {
        opacity: 1;
    }
</style>

<div class="modal shadow" id="TagsModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">

                <p class="text-center text-info">
                    Select Tags
                </p>

                <div class="container-fluid searchable-container">
                    <div class="row">
                        <?php
                        $i = 0;
                        foreach ($tags_suggested as $t) {
                            ?>
                            <div class="col items">
                                <div class="info-block block-info clearfix">
                                    <div class="square-box pull-left">
                                        <span class="glyphicon glyphicon-tags glyphicon-lg"></span>
                                    </div>
                                    <div data-toggle="buttons" class="btn-group bizmoduleselect">
                                        <label class="btn btn-default">
                                            <div class="bizcontent">
                                                <input type="checkbox" id="tag-{{ $i }}" name="" autocomplete="off">
                                                <span class="glyphicon glyphicon-ok glyphicon-lg"></span>
                                                <h5>
                                                    {{ $t }}
                                                </h5>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php $i++;
                        } ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="tags-done" type="button" class="btn btn-primary">Finish</button>
            </div>
        </div>
    </div>
</div>

@endsection