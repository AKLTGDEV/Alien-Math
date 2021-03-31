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
            <li class="nav-item">
                <a class="nav-link" href="{{ route('viewclassroomtimeline', [$class->id]) }}">Timeline</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('class_stats', [$class->id]) }}">Stats</a>
            </li>
        </ul>
    </div>
</nav>

<script type="text/javascript" src="{{ config('app.url') }}/thirdparty/bs-charts.js"></script>
<script type="text/javascript" src="{{ config('app.url') }}/thirdparty/chart.min.js"></script>

<?php

use Illuminate\Support\Facades\Input;
?>

<script>
    jQuery(document).ready(function($) {
        //TODO
    })
</script>

<div class="container-fluid">
    <div class="row mt-2">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header">
                    General Stats
                </div>
                <div class="card-body">
                    <h3 class="card-title text-center">
                        Collection: {{ "$collection->name" }}
                    </h3>
                    <div class="card-text text-center mt-1">
                        <ul>
                            <li>{{ $general['qcount'] }} questions</li>
                            <li>Attempt Rate: {{ $general['attempt'] }}%</li>
                            <li>Success Rate: {{ $general['success'] }}%</li>
                            <li>Time Spent: {{ $general['time'] }}s</li>
                            <li>Flick: {{ $general['flick'] }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Worksheets
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Questions</th>
                                <th scope="col">Attempt</th>
                                <th scope="col">Success</th>
                                <th scope="col">Time</th>
                                <th scope="col">Flick</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($wsitems as $wsitem)
                            <tr>
                                <th scope="row">{{ $wsitem['name'] }}</th>
                                <td>{{ $wsitem['questions'] }}</td>
                                <td>{{ $wsitem['attempt_rate'] }}%</td>
                                <td>{{ $wsitem['success_rate'] }}%</td>
                                <td>{{ $wsitem['net_time'] }}s</td>
                                <td>{{ $wsitem['flick_rate'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Students
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Attempt</th>
                                <th scope="col">Success</th>
                                <th scope="col">Time</th>
                                <th scope="col">Flick</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($studentitems as $student)
                            <tr>
                                <th scope="row">{{ $student['name'] }}</th>
                                <td>{{ $student['attempt_rate'] }}%</td>
                                <td>{{ $student['success_rate'] }}%</td>
                                <td>{{ $student['net_time'] }}s</td>
                                <td>{{ $student['flick_rate'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Settings
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <form action="{{ route('class_coll_rename', [$class->id, $collection->encname]) }}" method="post">
                                {{ csrf_field() }}
                                <div class="input-group">
                                    <input name="name" placeholder="New Name" class="form-control" type="text" />
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-primary" type="submit">
                                            Rename Collection
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <form action="{{ route('class_coll_delete', [$class->id, $collection->encname]) }}" method="post">
                                {{ csrf_field() }}
                                <div class="input-group">
                                    <button class="btn btn-outline-danger" type="submit">
                                        Delete Collection
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection