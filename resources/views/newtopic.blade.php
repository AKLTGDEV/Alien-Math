@extends('layouts.app')

@section('content')

@if ($eligible)

<style>
    #content {
        background: #fff;
        margin-top: 1%;
        margin-bottom: 1%;
    }
</style>

<script>
    jQuery(document).ready(function() {
        //
    })
</script>

<div class="container-fluid main">
    <div class="row">
        <div class="col-12">
            <div id="content" id="content">
                <div class="container-fluid">
                    <div class="d-sm-flex justify-content-between align-items-center mb-4 py-2">
                        <h3 class="text-dark mb-0">
                            Request Topics
                        </h3>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            @if (session()->has('status') && session()->get('task') == 'request')
                                            <div class="alert alert-info">
                                                {{ session('message') }}
                                            </div>
                                            @endif
                                            <form action="{{ route('reqtopics_sub') }}" method="post">
                                                {{ csrf_field() }}
                                                <div class="input-group mb-1">
                                                    <input name="name" type="text" class="form-control" placeholder="Topic Name">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-primary" type="submit">Request</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="col-6">
                                            <form action="{{ route('adminwork') }}" method="post">
                                                {{ csrf_field() }}
                                                <div class="input-group mb-1">
                                                    <input type="text" class="form-control" placeholder="Search Topic Requests">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-info" type="submit">Search</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="container-fluid">
                            <h3 class="text-dark mb-0 lead">
                                Trending Requests
                            </h3>
                            <div class="d-sm-flex justify-content-between align-items-center mb-4 py-2">
                                <div class="container-fluid">
                                    @if (session()->has('status') && session()->get('task') == 'support')
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="alert alert-info">
                                                {{ session('message') }}
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    <?php foreach ($requests as $request) { ?>
                                        <div class="row mt-1">
                                            <div class="col-4">
                                                <div class="card">
                                                    <div class="card-body">
                                                        {{ $request[0]->name }}
                                                    </div>
                                                    <div class="card-footer">
                                                        {{ $request[0]->people }} are supporting
                                                        <a class="btn btn-info btn-sm d-none d-sm-inline-block" role="button" href="{{ route('reqtopics_support', [$request[0]->id]) }}">
                                                            Support
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                @if (count($request) >= 2)
                                                <div class="card">
                                                    <div class="card-body">
                                                        {{ $request[1]->name }}
                                                    </div>
                                                    <div class="card-footer">
                                                        {{ $request[1]->people }} are supporting
                                                        <a class="btn btn-info btn-sm d-none d-sm-inline-block" role="button" href="{{ route('reqtopics_support', [$request[1]->id]) }}">
                                                            Support
                                                        </a>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="col-4">
                                                @if (count($request) == 3)
                                                <div class="card">
                                                    <div class="card-body">
                                                        {{ $request[2]->name }}
                                                    </div>
                                                    <div class="card-footer">
                                                        {{ $request[2]->people }} are supporting
                                                        <a class="btn btn-info btn-sm d-none d-sm-inline-block" role="button" href="{{ route('reqtopics_support', [$request[2]->id]) }}">
                                                            Support
                                                        </a>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@else

<link rel="stylesheet" href="{{ asset('css/newws.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<div class="global-container">
    <div class="card login-form">
        <div class="card-body">
            <h2 class="card-title text-center">
                Request Topics
            </h2>
            <div class="card-text text-center mt-1">
                To request new Topics, You must have a rating of 100, just {{ 100-$rating }} rating points less than what you have now. Come back soon!
            </div>
        </div>
    </div>
</div>


@endif

@endsection