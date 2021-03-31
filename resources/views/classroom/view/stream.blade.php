@extends('layouts.app')

@section('content')


<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.16/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.16/dist/summernote-bs4.min.js"></script>

<!-- Class NAV -->
<nav class="navbar navbar-expand navbar-light bg-light">
    <div class="d-flex">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsingNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
    <div class="navbar-collapse collapse justify-content-center" id="collapsingNavbar">
        <ul class="navbar-nav">
            <li class="nav-item active">
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

<script>
    $(document).ready(function() {

        function inputNumber(el) {
            var min = el.attr('min') || false;
            var max = el.attr('max') || false;

            var els = {};

            els.dec = el.prev();
            els.inc = el.next();

            el.each(function() {
                init($(this));
            });

            function init(el) {

                els.dec.on('click', decrement);
                els.inc.on('click', increment);

                function decrement() {
                    var value = el[0].value;
                    value--;
                    if (!min || value >= min) {
                        el[0].value = value;
                    }
                }

                function increment() {
                    var value = el[0].value;
                    value++;
                    if (!max || value <= max) {
                        el[0].value = value++;
                    }
                }
            }
        }

        $("#post_note").summernote();
        inputNumber($('.input-number'));

        $("#upload-paper-btn").click(function(e) {
            $("#UploadModal").modal('show')
        })

        $("#upload-ws-json-btn").click(function(e) {
            $("#WSJSONModal").modal('show')
        })
    })
</script>

<?php

use App\UserModel;
?>

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
                
                STREAM

            </div>
        </div>
    </div>
</div>


@endsection