@extends('layouts.app')

@section('content')

<style>
    #content {
        background: #fff;
        margin-top: 1%;
        margin-bottom: 1%;
    }

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

<script>
    $(document).ready(function() {
        inputNumber($('.input-number'));
    })

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
</script>

<script type="text/javascript" src="{{ config('app.url') }}/thirdparty/bs-charts.js"></script>
<script type="text/javascript" src="{{ config('app.url') }}/thirdparty/chart.min.js"></script>

<?php

use App\groups;

$isoperator = groups::isoperator(Auth::user()->username);
$isadmin = groups::ismod(Auth::user()->username);
?>

<div class="container-fluid main">
    <div class="row">
        <div class="col-12">
            <div id="content" id="content">
                <div class="container-fluid">
                    <div class="d-sm-flex justify-content-between align-items-center mb-4 py-2">
                        <h3 class="text-dark mb-0">Dashboard</h3>
                    </div>

                    <div class="row">
                        <div class="col-md-6">

                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="text-primary font-weight-bold m-0">
                                        General Info
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <h6 class="text-success font-weight-bold m-0">
                                        <?php

                                        use App\User;

                                        $latest = User::find(User::max('id'));
                                        ?>
                                        {{ User::count() }} Total users, <a class="text-success" href="{{ route('namedprofile', [$latest->username]) }}">{{ "@" . $latest->username }}</a> is the latest one.
                                    </h6>
                                </div>
                            </div>

                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="text-primary font-weight-bold m-0">Create Users</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admincreateuser') }}" method="post">
                                        {{ csrf_field() }}
                                        <div class="form-horizontal">
                                            <input type="text" class="mt-1 form-control" placeholder="Name" aria-label="Name" name="name" required>
                                            <input type="text" class="mt-1 form-control" placeholder="Username" aria-label="username" name="username" required>
                                            <input type="email" class="mt-1 form-control" placeholder="Email" aria-label="email" name="email">
                                            <input type="text" class="mt-1 form-control" placeholder="Password" aria-label="password" name="password">
                                            <input type="text" class="mt-1 form-control" placeholder="Bio" aria-label="username" name="bio">
                                            <button class="mt-1 btn btn-outline-primary" type="submit">
                                                Add
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            @if($isoperator || $isadmin)

                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="text-primary font-weight-bold m-0">Login As User</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('adminloginuser') }}" method="post">
                                        {{ csrf_field() }}
                                        <div class="form-horizontal">
                                            <input type="text" class="mt-1 form-control" placeholder="Username" aria-label="Username" name="username" required>
                                            <button class="mt-1 btn btn-outline-primary" type="submit">
                                                Login
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            @endif
                            @if($isadmin)

                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="text-primary font-weight-bold m-0">Tags</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('adminwork') }}" method="post">
                                        {{ csrf_field() }}
                                        <div class="input-group mb-3">
                                            <input type="text" name="work" value="TAGS.NEW" style="display: none;">
                                            <input type="text" class="form-control" placeholder="Name" aria-label="Name" name="tagname">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-primary" type="submit">
                                                    Add
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <form action="{{ route('adminwork') }}" method="post">
                                        {{ csrf_field() }}
                                        <div class="input-group mb-3">
                                            <input type="text" name="work" value="TAGS.DEL" style="display: none;">
                                            <input type="text" class="form-control" placeholder="Name" aria-label="Name" name="tagname">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-primary" type="submit">
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="text-danger font-weight-bold m-0">Danger Zone</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('adminwork') }}" method="post">
                                        {{ csrf_field() }}
                                        <input type="text" name="work" value="PURGE" style="display: none;">
                                        <button class="btn btn-outline-danger shadow">Purge</button>
                                    </form>
                                </div>
                            </div>

                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="container-fluid">

                                @if($isadmin)

                                <div class="row">
                                    <div class="col-12">
                                        <div class="card shadow mb-4">
                                            <div class="card-header py-3">
                                                <span>
                                                    <span class="text-primary font-weight-bold m-0">Pending Docs</span>
                                                    <a class="btn btn-info btn-sm d-none d-sm-inline-block" role="button" href="#">List All</a>
                                                </span>
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-group">
                                                    <div class="row">
                                                        @if (count($docs) == 0)
                                                        <div class="col-md">
                                                            <li class="list-group-item">
                                                                None
                                                            </li>
                                                        </div>
                                                        @else
                                                        <?php foreach ($docs as $doc) { ?>
                                                            <div class="col-md">
                                                                <li class="list-group-item">
                                                                    <a href="{{ route('admindocindex', [$doc['id']]) }}">
                                                                        <div class="lead text-primary">{{ $doc['title'] }}</div>
                                                                    </a>
                                                                    <div class="text-info">To be completed in {{ $doc['time'] }} minutes</div>
                                                                    <div class="text-info">Status: {{ $doc['accepted'] == 1 ? "Accepted & Posted" : "Pending" }}</div>
                                                                </li>
                                                            </div>
                                                        <?php } ?>
                                                        @endif
                                                    </div>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @endif

                                @if($isoperator)
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card shadow mb-4">
                                            <div class="card-header py-3">
                                                <span>
                                                    <span class="text-primary font-weight-bold m-0">Worksheet Actions</span>
                                                </span>
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-group">
                                                    <div class="row">
                                                        <div class="col">
                                                            <li class="list-group-item">
                                                                <form action="{{ route('admincomposews') }}" method="get">
                                                                    {{ csrf_field() }}
                                                                    <div class="form-horizontal">
                                                                        <div class="form-group">
                                                                            <span class="input-number-decrement">–</span><input class="input-number" type="text" value="1" min="0" max="50" name="nos"><span class="input-number-increment">+</span>
                                                                        </div>
                                                                    </div>

                                                                    <button type="submit" class="mt-1 btn btn-sm btn-outline-primary">Compose</button>
                                                                </form>
                                                            </li>
                                                        </div>
                                                    </div>

                                                    <div class="row mt-1">
                                                        <div class="col">
                                                            <li class="list-group-item">
                                                                <form action="{{ route('adminpostws') }}" method="post" enctype="multipart/form-data">
                                                                    {{ csrf_field() }}
                                                                    <div class="form-horizontal">
                                                                        <div class="form-group">
                                                                            <input value="" class="form-control" type="text" name="username" placeholder="Post as @username" required>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <input name="data_json" type='file' id="uploadJSON" accept=".json" required />
                                                                        </div>

                                                                        <button type="submit" class="mt-1 btn btn-sm btn-outline-primary">Post WS</button>
                                                                    </div>
                                                                </form>
                                                            </li>
                                                        </div>
                                                    </div>

                                                    <div class="row mt-1">
                                                        <div class="col">
                                                            <li class="list-group-item">
                                                                <form action="{{ route('adminpreviewws') }}" method="post" enctype="multipart/form-data">
                                                                    {{ csrf_field() }}
                                                                    <div class="form-horizontal">
                                                                        <div class="form-group">
                                                                            <input name="data_json" type='file' id="uploadJSON" accept=".json" required />
                                                                        </div>

                                                                        <button type="submit" class="mt-1 btn btn-sm btn-outline-primary">Preview</button>
                                                                    </div>
                                                                </form>
                                                            </li>
                                                        </div>
                                                    </div>

                                                    <div class="row mt-1">
                                                        <div class="col">
                                                            <li class="list-group-item">
                                                                <form action="{{ route('adminexplodews') }}" method="post" enctype="multipart/form-data">
                                                                    {{ csrf_field() }}
                                                                    <div class="form-horizontal">
                                                                        <div class="form-group">
                                                                            <input value="" class="form-control" type="text" name="username" placeholder="Post as @username" required>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <input name="data_json" type='file' id="uploadJSON" accept=".json" required />
                                                                        </div>

                                                                        <button type="submit" class="mt-1 btn btn-sm btn-outline-primary">
                                                                            Explode as Questions
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </li>
                                                        </div>
                                                    </div>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($isadmin)

                                <div class="row">
                                    <div class="col-12">
                                        <div class="card shadow mb-4">
                                            <div class="card-header py-3">
                                                <span class="text-primary font-weight-bold m-0">
                                                    Mails
                                                </span>
                                            </div>
                                            <div class="card-body">
                                                <form action="{{ route('adminmail') }}" method="get">
                                                    {{ csrf_field() }}
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">@</div>
                                                        </div>
                                                        <input type="text" class="form-control" placeholder="Username" aria-label="Username" name="username">
                                                        <select name="mailtype" class="form-control">
                                                            <option value="demo">Demo</option>
                                                            <option value="welcome">Welcome</option>
                                                            <option value="newpapers">New Papers</option>
                                                        </select>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-outline-primary" type="submit">
                                                                Send
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="card shadow mb-4">
                                            <div class="card-header py-3">
                                                <span class="text-primary font-weight-bold m-0">
                                                    Misc
                                                </span>
                                            </div>
                                            <div class="card-body">
                                                <a href="{{ route('admin_post_slug') }}" class="btn btn-sm btn-outline-secondary">
                                                    Generate Slug for Posts
                                                </a>
                                                <a href="{{ route('admin_ws_slug') }}" class="btn btn-sm btn-outline-secondary">
                                                    Generate Slug for Worksheets
                                                </a>
                                                <a href="{{ route('admin_purge_slug') }}" class="btn btn-sm btn-outline-secondary">
                                                    Purge Slug for Posts
                                                </a>
                                                <a href="{{ route('admin_purge_ws_slug') }}" class="btn btn-sm btn-outline-secondary">
                                                    Purge Slug for Worksheets
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="card shadow mb-4">
                                            <div class="card-header py-3">
                                                <span class="text-primary font-weight-bold m-0">
                                                    JSON Edits
                                                </span>
                                            </div>
                                            <div class="card-body">
                                                <form action="{{ route('admin_jsonedit') }}" method="get">
                                                    {{ csrf_field() }}
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">@</div>
                                                        </div>
                                                        <input type="text" class="form-control" placeholder="Username" aria-label="Username" name="username">
                                                        <select name="mailtype" class="form-control">
                                                            <option value="actilog">Activity Log</option>
                                                            <option value="answers">Answers</option>
                                                            <option value="ext">Extended Info</option>
                                                            <option value="tags">Tags</option>
                                                            <option value="drecord">Daily Record</option>
                                                        </select>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-outline-primary" type="submit">
                                                                Edit
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endsection