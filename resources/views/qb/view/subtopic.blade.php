@extends('layouts.app')

@section('content')

<?php

use App\users;
use Illuminate\Support\Facades\Auth;

$ajax_url = route('qbank_listq_subtopic', [$subtopic->id]);
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
            <h2>
                Sub-Topic: <span class="badge badge-info">{{$subtopic->name}}</span>
            </h2>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link active" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">
                        Questions
                    </a>
                    <a class="nav-link" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">
                        Statistics
                    </a>
                    <a class="nav-link" id="v-pills-settings-tab" data-toggle="pill" href="#v-pills-settings" role="tab" aria-controls="v-pills-settings" aria-selected="false">
                        Settings
                    </a>
                </div>
            </div>
            <div class="col-md-10">
                <div class="tab-content" id="v-pills-tabContent">
                    <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
                        <div class="form-group">

                            <div class="row" id="posts_holder">
                            </div>

                            <div class="row mt-2">
                                <div class="col-12" id="req">
                                    <div style="width: 100%" class="mt-2 btn btn-outline-primary" id="req-text">
                                        Load Posts
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                        Coming Soon
                    </div>
                    <div class="tab-pane fade" id="v-pills-settings" role="tabpanel" aria-labelledby="v-pills-settings-tab">
                        Coming Soon
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

@endsection