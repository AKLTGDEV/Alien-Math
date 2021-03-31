@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/newws.css') }}">

<?php

use Illuminate\Support\Facades\Input;
?>

<script>
    $(document).ready(function() {
        $("title").text(`{{ $ws['title'] }} - Worksheet by {{ "@$author->username" }}`);
        $("#start").click(function(e) {
            window.location.href = "{{ route('TSanswer', [$TS->encname, $wsname]) }}";
        })
    })
</script>

<div class="global-container">
    <div class="card login-form">
        <div class="card-body">
            <h3 class="card-title text-center">
                Worksheet - {{ $ws['title'] }}
            </h3>
            <div class="card-text">
                <h6>By {{ $author->name }}</h6>

                <div class="row">
                    <ul>
                        <li>{{ $ws['nos'] }} Questions</li>
                        <li>To be completed in {{ $ws['time'] }} Minutes</li>
                        <li>0 attemptees so far</li>
                        <li class="text-danger">Ensure you have a good connection before attempting. Do not press back/refresh the page during the test.</li>
                    </ul>
                </div>
                <div class="row" style="margin-top:3%">
                    <div class="col-md-10 col-md-offset-1">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary" style="float: left">
                            Go Back
                        </a>
                        <button id="start" class="btn btn-info" style="float: right">
                            Start
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection