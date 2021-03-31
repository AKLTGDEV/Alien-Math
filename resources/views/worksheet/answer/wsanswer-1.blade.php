@extends('layouts.app')

@section('content')


<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
?>

<script>
    $(document).ready(function() {
        $("title").text(`{{ $ws->title }} - Worksheet by {{ "@$author->username" }}`);
        var logged_in = "{{ Auth::check() == true ? '1' : '0' }}";

        if (logged_in == 1) {
            $("#start").click(function(e) {
                window.location.href = "{{ route('wsanswer-2', [$ws->slug]) }}";
            })
        } else {
            $("#start").click(function(e) {
                window.location.href = "{{ route('public-wsanswer-2', [$ws->slug]) }}";
            })
        }
    })
</script>

<div class="global-container">
    <div class="row mt-2">
        <div class="col-12 col-md-8 ml-md-auto mr-md-auto">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title text-center">
                        Worksheet - {{ $ws->title }}
                    </h3>
                    <div class="card-text">
                        <h6>By {{ $author->name }}</h6>

                        <div class="row">
                            <ul>
                                <li>{{ $ws->nos }} Questions</li>
                                <li>To be completed in {{ $ws->mins }} Minutes</li>
                                <li>{{ $ws->attempts }} attemptees so far</li>
                                <li class="text-danger">Ensure you have a good connection before attempting. Do not press back/refresh the page during the test.</li>
                            </ul>
                        </div>
                        <div class="row" style="margin-top:3%">
                            <div class="col-md-10 col-md-offset-1">
                                <a href="{{ url()->previous() }}" class="btn btn-secondary" style="float: left">
                                    Go Back
                                </a>
                                <div class="ml-1 fb-share-button" data-href="{{Request::url()}}" data-layout="button_count" data-size="large" style="float: left;">
                                    <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fdevelopers.facebook.com%2Fdocs%2Fplugins%2F&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">
                                        Share
                                    </a>
                                </div>
                                <button id="start" class="btn btn-info" style="float: right">
                                    Start
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection