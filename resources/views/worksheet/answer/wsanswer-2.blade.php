@extends('layouts.app')

@section('content')

<?php

use App\UserModel;

$nos = $ws->nos;
$wsid = $ws->id;

?>


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

    .answer-holder {
        background: rgba(0, 140, 163, 0.17);
    }
</style>

@include('logic.answerws')

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
                    </div>
                </div>

                <div class="card-body container-fluid" id="content-body">

                    <input type="text" style="display: none;" id="current" value="1">
                    <input type="text" style="display: none;" id="current-type" value="">

                    <div class="card">
                        <div class="card-header" id="question_content">
                        </div>
                        <div class="card-body">
                            <div id="answer-holder">
                            </div>
                        </div>
                        <div class="card-footer" id="answer">
                            <div class="answer-holder">
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-top:1%">
                        <div class="col-md-10 col-md-offset-1">
                            <button id="hint" class="btn btn-outline-info">
                                Hint
                            </button>
                            <button id="subq" class="btn btn-info">
                                Submit
                            </button>
                            <button id="nextq" class="btn btn-info" disabled>
                                Next Question
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection