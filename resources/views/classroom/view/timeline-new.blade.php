@extends('layouts.app')
@section('content')

<!-- Class NAV -->
<nav class="navbar navbar-expand navbar-light bg-light">
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
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('viewclassroomtimeline', [$class->id]) }}">Timeline</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('class_stats', [$class->id]) }}">Stats</a>
            </li>
        </ul>
    </div>
</nav>

@include('classroom.includes.timeline-logic')

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
                        <div class="card feed_item">
                            <div class="card-body" id="feed-holder">
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
</div>

@endsection