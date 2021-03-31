@extends('layouts.app')

@section('content')

<?php

use App\UserModel;

?>

<!-- Class NAV -->
<nav class="navbar navbar-expand-md navbar-light bg-light">
    <div class="d-flex">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsingNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
    <div class="navbar-collapse collapse justify-content-center" id="collapsingNavbar">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('viewclassroom', [$class->id]) }}">Class</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('viewclassroomtimeline', [$class->id]) }}">Timeline</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('viewclassroompeople', [$class->id]) }}">People</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('class_stats', [$class->id]) }}">Stats</a>
            </li>
        </ul>
    </div>
</nav>

<script>
    $(document).ready(function() {})
</script>

<div class="container-fluid">
    <div class="row">
        <div class="offset-md-1 col-md-10">
            <style>
                .jumbotron {
                    color: white;
                    background-image: url('{{ config('app.url') }}/images/bg4.png');
                    height: 50vh;
                }
            </style>
            <div class="jumbotron jumbotron-fluid mt-1 shadow">
                <div class="container">
                    <h1 class="display-4">
                        <b>{{ $class->name }}</b>
                    </h1>
                    <p>
                        {{ $class->users }} Members <br>
                        #{{ substr($class->encname, 0, 6) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="offset-md-1 col-md-10">
        <div class="row">
            <div class="col-sm-4">
                @include('classroom.includes.pending')
            </div>
            <div class="col-sm-8">
                <div class="row">
                    <div class="col-12">
                        <div class="card" id="upcomingWork">
                            <div class="card-header">
                                Invite Students <a class="action" href="#"><i class="fas fa-bars"></i></a>
                            </div>
                            <div class="card-body">
                                @if (session()->has('status'))
                                <div class="row">
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            {{ session('message') }}
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <form id="inv" action="{{ route('classroomsendinvite', [$class->id]) }}" method="post" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="input-group">

                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-question-circle">
                                                </i>
                                            </div>
                                        </div>
                                        <input name="username" placeholder="@username" id="username" type="text" class="form-control" aria-label="username" value="">
                                        <button type="submit" class="btn btn-secondary form-control">Invite</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card" id="upcomingWork">
                            <div class="card-header">
                                Pending List <a class="action" href="#"><i class="fas fa-bars"></i></a>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">

                                    @if (count($pendinglist) == 0)

                                    No Pending invites

                                    @else

                                    @foreach ($pendinglist as $pmember)
                                    <?php
                                    $p = UserModel::where("username", $pmember)->first();
                                    ?>

                                    <li class="list-group-item">
                                        <a href="{{ route('namedprofile', [$p->username]) }}">{{ "@".$p->username }}</a>
                                    </li>
                                    @endforeach

                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card" id="upcomingWork">
                            <div class="card-header">
                                Members <a class="action" href="#"><i class="fas fa-bars"></i></a>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    @foreach ($memberlist as $member)
                                    <?php
                                    $m = UserModel::where("username", $member)->first();
                                    ?>

                                    <li class="list-group-item">
                                        <a href="{{ route('namedprofile', [$m->username]) }}">
                                            <img class="rounded-circle" src="{{ config('app.url') }}/user/{{ $m->username }}/profilepic" height="30" alt="{{ $m->name }}">
                                            {{ "@".$m->username }}
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>

@endsection