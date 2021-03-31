@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/answs.css') }}">
<script src="{{ asset('js/answs.js') }}"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<?php

$nos = $ws['nos'];
$bodies = $ws['bodies'];
$opts = $ws['opts'];
$corrects = $ws['correct'];

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
</style>

<div class="container">
    <div class="row">
        <div class="col-12 holder-col">
            <div class="card card-default px-2 py-1">
                <div class="card-heading">
                    <div class="row">
                        <div class="col-md-10">
                            <h4>Preview of {{ $ws['title'] }}</h4>
                        </div>
                        <div class="col-md-2">
                            <a id="clockdiv" class="btn btn-outline-primary btn-sm shadow" role="button" href="#">
                                &nbsp;Generate Report
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body container-fluid">
                    <?php
                    for ($i = 1; $i <= $nos; $i++) {
                        $corr = $corrects[$i - 1];
                        ?>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>
                                            <b>
                                                Question {{ $i }}
                                            </b>
                                        </h4>
                                        <?php echo $bodies[$i - 1]; ?>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="option" opt="1">
                                                    <div class="option-text btn shadow btn-rounded waves-effect 
                                                        @if($corr == 1)
                                                        btn-success
                                                        @endif
                                                    ">
                                                        {{ $opts[$i - 1] [0] }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="option" opt="2">
                                                    <div class="option-text btn shadow  btn-rounded waves-effect 
                                                    
                                                        @if($corr == 2)
                                                        btn-success
                                                        @endif

                                                    ">
                                                        {{ $opts[$i - 1] [1] }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="option" opt="3">
                                                    <div class="option-text btn shadow  btn-rounded waves-effect 
                                                    
                                                    @if($corr == 3)
                                                    btn-success
                                                    @endif

                                                    ">
                                                        {{ $opts[$i - 1] [2] }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="option" opt="4">
                                                    <div class="option-text btn shadow  btn-rounded waves-effect 
                                                    
                                                    @if($corr == 4)
                                                    btn-success
                                                    @endif

                                                    ">
                                                        {{ $opts[$i - 1] [3] }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 mt-1">
                                                <b>Option {{ $corr }} is correct</b>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php } ?>

                    <div class="row" style="margin-top:1%">
                        <div class="col-md-10 col-md-offset-1">
                            <a id="sub" class="btn btn-info" href="{{ url()->previous() }}">
                                Go Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection