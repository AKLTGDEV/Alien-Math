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
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body" id="say">
                                <form id="note_f" action="{{ route('CLR_postnote', [$class->id]) }}" method="post" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <textarea id="post_note" name="note" style="width: 100%"></textarea>
                                    <button class="btn btn-sm btn-primary" type="submit">Post Note</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($isadmin)
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                Post Content
                            </div>
                            <div class="card-body" id="NewContent">
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <span id="upload-paper-btn">
                                            <i class="fas fa-book">
                                            </i>
                                            Upload Paper
                                        </span>
                                    </li>
                                    <li class="list-group-item">
                                        <span id="upload-ws-json-btn">
                                            <i class="fas fa-book">
                                            </i>
                                            Upload Worksheet JSON
                                        </span>
                                    </li>
                                    <li class="list-group-item">
                                        <span>
                                            <a href="{{ route('CLR_postq', [$class->id]) }}">
                                                <i class="fas fa-question-circle">
                                                </i>
                                                Post Question
                                            </a>
                                        </span>
                                    </li>
                                    <li class="list-group-item">
                                        <span>
                                            <a href="" data-toggle="modal" data-target="#postwsModal">
                                                <i class="fas fa-question-circle">
                                                </i>
                                                Post Worksheet
                                            </a>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>


                @if($isadmin)
                <div class="row mb-2">
                    <div class="col-12">
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

                <div class="row">
                    <div class="col-12">
                        <div class="card" id="upcomingWork">
                            <div class="card-header">
                                Pending List
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
                                        <a href="{{ route('namedprofile', [$p->username]) }}">
                                            <img class="rounded-circle" src="{{ config('app.url') }}/user/{{ $p->username }}/profilepic" height="30" alt="{{ $p->name }}">
                                            {{ "@".$p->username }}
                                        </a>
                                        <span>
                                            <a href="{{ route('class_user_removepending', [$class->id, $p->username]) }}" class="text-danger">
                                                <span class="fas fa-times">
                                                </span>
                                            </a>
                                        </span>
                                    </li>
                                    @endforeach

                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-12">
                        <div class="card" id="upcomingWork">
                            <div class="card-header">
                                Members
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
                                        <span>
                                            <a href="{{ route('class_user_remove', [$class->id, $m->username]) }}" class="text-danger">
                                                <span class="fas fa-times">
                                                </span>
                                            </a>
                                        </span>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                Class Admin
                            </div>
                            <div class="card-body" id="NewContent">
                                <ul class="list-group">
                                    <li class="card">
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                @if (session()->has('reset-att-status'))
                                                <div class="alert alert-{{ session('reset-att-status') }}">
                                                    {{ session('message') }}
                                                </div>
                                                @endif
                                                <form action="{{ route('class_ws_att_reset', [$class->id]) }}" method="get">
                                                    {{ csrf_field() }}
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">@</div>
                                                        </div>
                                                        <select name="username" class="form-control">
                                                            @foreach ($members as $u)
                                                            <option value="{{ $u }}">{{ $u }}</option>
                                                            @endforeach
                                                        </select>
                                                        <select name="ws" class="form-control">
                                                            @foreach ($worksheets as $w)
                                                            <option value="{{ $w['encname'] }}">{{ $w['title'] }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-outline-primary" type="submit">
                                                                Remove Attempt
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="card">
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                @if (session()->has('rename-status'))
                                                <div class="alert alert-{{ session('rename-status') }}">
                                                    {{ session('message') }}
                                                </div>
                                                @endif
                                                <form action="{{ route('class_rename', [$class->id]) }}" method="post">
                                                    {{ csrf_field() }}
                                                    <div class="input-group">
                                                        <input name="name" placeholder="New Name" class="form-control" type="text" />
                                                        <div class="input-group-append">
                                                            <button class="btn btn-outline-primary" type="submit">
                                                                Rename Class
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </li>
                                            <li class="list-group-item">
                                                <form action="{{ route('class_delete', [$class->id]) }}" method="get">
                                                    {{ csrf_field() }}
                                                    <div class="input-group">
                                                        <button class="btn btn-outline-danger" type="submit">
                                                            Delete Class
                                                        </button>
                                                    </div>
                                                </form>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>


<style>
    .input-number {
        width: 80px;
        padding: 0 12px;
        vertical-align: top;
        text-align: center;
        outline: none;
    }

    .input-number,
    .input-number-decrement,
    .input-number-increment {
        border: 1px solid #ccc;
        height: 40px;
        user-select: none;
    }

    .input-number-decrement,
    .input-number-increment {
        display: inline-block;
        width: 30px;
        line-height: 38px;
        background: #f1f1f1;
        color: #444;
        text-align: center;
        font-weight: bold;
        cursor: pointer;
    }

    .input-number-decrement:active,
    .input-number-increment:active {
        background: #ddd;
    }

    .input-number-decrement {
        border-right: none;
        border-radius: 4px 0 0 4px;
    }

    .input-number-increment {
        border-left: none;
        border-radius: 0 4px 4px 0;
    }
</style>

<div class="modal" tabindex="-1" role="dialog" id="postwsModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('CLR_postws', [$class->id]) }}" method="get" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Post Worksheet</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>
                            Number of questions
                        </label>
                        <div class="row">
                            <div class="col-md-12">
                                <span class="input-number-decrement">–</span><input id="nos_Q" class="input-number" type="text" value="1" min="0" max="50" name="nos"><span class="input-number-increment">+</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Proceed</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal shadow" id="UploadModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="lead text-center text-secondary mb-2">
                    Upload Paper
                </div>

                <p class="text-center text-info">
                    Upload the Question paper in PDF format, and we'll design and upload the Worksheet according to that. Note that the correct options for each question must be highlighted properly.
                    We'll notify you once we are done. :-)
                </p>
                <form action="{{ route('class_docupload', [$class->id]) }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-horizontal">
                        <div class="form-group">
                            <input value="" class="form-control" type="text" name="title" placeholder="Title of the WS" required>
                        </div>
                        <div class="form-group">
                            <input value="" class="form-control" type="text" name="notes" placeholder="Notes for staff..">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Time (in minutes)</label>
                            <span class="input-number-decrement">
                                –
                            </span>
                            <input class="input-number" type="text" value="1" min="0" max="50" name="time" required>
                            <span class="input-number-increment">
                                +
                            </span>
                        </div>

                        <div class="form-group">
                            <input name="doc" type='file' id="PDFupload" accept=".pdf, .docx, .doc" required />
                        </div>

                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">
                                Upload
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal shadow" id="WSJSONModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="lead text-center text-secondary mb-2">
                    Upload Worksheet JSON
                </div>

                <p class="text-center text-info">
                    Upload The Worksheet JSON, and the WS will be posted accordingly
                </p>
                <form action="{{ route('class_ws_json', [$class->id]) }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-horizontal">
                        <div class="form-group">
                            <input name="data_json" type='file' id="json_upload" accept=".json" required />
                        </div>

                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">
                                Upload
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection