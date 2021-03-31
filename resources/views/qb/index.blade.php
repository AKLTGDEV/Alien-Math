@extends('layouts.app')

@section('content')

<?php

use App\users;
use Illuminate\Support\Facades\Auth;

$ajax_url = route('qbank_listq');
$qb_index = true;
$user = Auth::user();
?>

@include('qb.loadq')

<div class="container-fluid main">
    <div class="row mt-2">
        <div class="col-12">
            @include('qb.nav')
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-12">
            <a class="btn btn-sm btn-outline-primary" href="{{ route('qbank_newq_mcq') }}">
                Add MCQ <span class="ml-1 fas fa-plus"></span>
            </a>
            <a class="btn btn-sm btn-outline-primary" href="{{ route('qbank_newq_subjective') }}">
                Add Subjective Question <span class="ml-1 fas fa-plus"></span>
            </a>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-12">
            @if ( count( $errors ) > 0 )
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                {{ $error }}<br>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <div class="row mt-2" id="posts_holder">
    </div>

    <div class="row mt-2">
        <div class="col-12" id="req">
            <div style="width: 100%" class="mt-2 btn btn-outline-primary" id="req-text">
                Load Posts
            </div>
        </div>
    </div>

</div>


<style>
    body {
        padding-bottom: 70px;
    }

    #bottom-nav {
        background: #546e7a;
    }
</style>
<nav id="bottom-nav" class="navbar navbar-expand-md navbar-dark fixed-bottom">
    <div class="container-fluid">
        <button data-toggle="collapse" class="navbar-toggler" data-target="#navcol-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navcol-1">
            <ul class="nav navbar-nav ml-auto">
                <li class="nav-item" role="presentation">
                    <div class="nav-link" id="current-test-proceed">
                        <span id="current-test-qnos">0</span> Questions Selected. <span class="text-bold mr-1">Make Worksheet</span><span class="fas fa-arrow-right"></span>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    </div>
</nav>

@endsection