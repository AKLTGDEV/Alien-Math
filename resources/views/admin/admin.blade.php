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
                                            
                                            <div class="form-group">
                                                <label>User Type</label>
                                                <select class="form-control" name="utype">
                                                    <option value="admin">Admin</option>
                                                    <option value="creator">Teacher</option>
                                                    <option value="student">Student</option>
                                                </select>
                                            </div>
                                            
                                            <button class="mt-1 btn btn-outline-primary" type="submit">
                                                Add
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
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
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
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

                            @if($isoperator || $isadmin)

                            <div class="row">
                                <div class="col-12">
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
                                </div>
                            </div>

                            @endif
                            @if($isadmin)

                            <div class="row">
                                <div class="col-12">
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
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endsection