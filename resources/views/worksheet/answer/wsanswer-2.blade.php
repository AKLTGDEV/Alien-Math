@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/answs.css') }}">
<script src="{{ asset('js/answs.js') }}"></script>

<?php

use App\UserModel;

$nos = $ws->nos;
$wsid = $ws->id;

?>

@include('logic.answerws')

<style>
    .option {
        height: 50px;
        margin: 1%;

    }

    .option-text {
        width: 100%;
        height: 100%;
        text-align: center;
    }

    .opts-holder {
        margin-left: 2%;
        margin-right: 2%;
    }

    .opt-selected {
        background: linear-gradient(90deg, rgba(254, 188, 188, 1) 0%, rgba(250, 228, 167, 1) 100%);
    }

    .holder-col {
        margin-top: 5px;
    }
</style>

<div class="container">
    <div class="row">
        <div class="col-12 holder-col">
            <div class="card card-default px-2 py-1">
                <div class="card-heading">
                    <div class="row">
                        <div class="col-md-10">
                            <h4>{{ $ws->title }}</h4>
                            <?php
                            $authorname = UserModel::where('id', $ws->author)->first()->name;
                            ?>
                            <h6>By {{ $authorname }}</h6>
                        </div>
                        <div class="col-md-2" id="clockdiv-holder">
                            <div id="clockdiv" class="btn btn-primary btn-md shadow" role="button">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body container-fluid" id="content-body">
                    <nav aria-label="Page navigation example">
                        <ul class="nav nav-tabs q-holder">
                            <li class="nav-item">
                                <a class="nav-link" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </li>

                            <?php
                            for ($i = 1; $i <= $ws->nos; $i++) {
                                ?>
                                <li class="nav-item" pid="{{ $i }}">
                                    <a class="nav-link" id="q-{{ $i }}-tab" data-toggle="tab" href="#body-q-{{ $i }}" role="tab" aria-controls="body-q-{{ $i }}" aria-selected="true" qid="{{$i}}">
                                        Q{{ $i }}
                                    </a>
                                </li>

                            <?php } ?>

                            <li class="nav-item">
                                <a class="nav-link" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <div class="tab-content clearfix" id="TabContent">

                        <?php
                        for ($i = 1; $i <= $ws->nos; $i++) {
                            ?>

                            <div class="tab-pane fade show" id="body-q-{{ $i }}" role="tabpanel" aria-labelledby="q-{{ $i }}-tab" pid="{{ $i }}">
                                <div class="card">
                                    <div class="card-header" id="question_content_{{ $i }}">
                                        <i class='fas fa-2x fa-spinner fa-spin'></i>
                                    </div>
                                    <div class="card-body">
                                        <!--
                                            This is the place where answer holders would be placed.

                                            MCQ: 4 Option boxes
                                            SAQ: One text input
                                            SQA: 4 Dropdowns

                                        -->
                                        <div id="answer-holder-{{ $i }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php } ?>

                    </div>
                    <div class="row" style="margin-top:1%">
                        <div class="col-md-10 col-md-offset-1">
                            <button id="sub" class="btn btn-info" type="submit">
                                Submit
                            </button>
                            <div class="btn btn-outline-success" id="refresh-cont-btn">
                                Re-load Questions
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection