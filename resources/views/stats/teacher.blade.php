@extends('layouts.app')
@section('content')
<style>
    #content {
        background: #fff;
        margin-top: 1%;
        margin-bottom: 1%;
        padding: 5px;
    }
</style>
<script type="text/javascript" src="{{ config('app.url') }}/thirdparty/bs-charts.js"></script>
<script type="text/javascript" src="{{ config('app.url') }}/thirdparty/chart.min.js"></script>

@include('stats.logic.teacher')

<div class="container-fluid main">
    <div class="row">
        <div class="col-12">
            <div id="content" id="content">
                <div class="container-fluid">
                    <div class="d-sm-flex justify-content-between align-items-center mb-4">
                        <h3 class="text-dark mb-0">Teachers' Dashboard</h3>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-xl-3 mb-4">
                            <div class="card shadow border-left-primary py-2">
                                <div class="card-body">
                                    <div class="row align-items-center no-gutters">
                                        <div class="col mr-2">
                                            <div class="text-uppercase text-primary font-weight-bold text-xs mb-1">
                                                <span>
                                                    Questions Posted
                                                </span>
                                            </div>
                                            <div class="text-dark font-weight-bold h5 mb-0">
                                                <span>{{$posts}}</span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-lg-7 col-xl-8">
                            <div class="card shadow mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="text-primary font-weight-bold m-0">Overview</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="dr_chart">
                                        </canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5 col-xl-4">
                            <div class="card shadow mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 id="topics-heading" class="text-primary font-weight-bold m-0">Topics</h6>
                                    <div class="dropdown no-arrow">
                                        <button class="btn btn-link btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false" type="button"><i class="fas fa-ellipsis-v text-gray-400"></i></button>
                                        <div class="dropdown-menu shadow dropdown-menu-right animated--fade-in" role="menu">
                                            <p class="text-center dropdown-header">Stats for:</p>
                                            <a href="#" id="topics-posted" class="dropdown-item active" role="presentation">&nbsp;Posted</a>
                                            <a href="#" id="topics-answered" class="dropdown-item" role="presentation">&nbsp;Answered</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="topics-chart">
                                        </canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--ROW-->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-sm-flex justify-content-between align-items-center">
                                <h3 class="text-dark mb-0">All Worksheets</h3>
                            </div>

                            <div class="mb-2">
                                <ul>
                                    <li>Active Worksheet: <span class="font-weight-bold" id="active-ws-head"></span></li>
                                    <li>Active User: <span class="font-weight-bold" id="active-user-head"></span></li>
                                    <li>Active Question: <span class="font-weight-bold" id="active-user-question"></span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-1 ml-1">
                        <div class="dropdown m-1">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButtonWorksheets" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" <?php if (count($worksheets) == 0) echo "disabled"; ?>>
                                Worksheets
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonWorksheets">
                                <?php foreach ($worksheets as $ws) { ?>
                                    <a wsid="{{ $ws->id }}" class="wsitem dropdown-item">{{ $ws->title }}</a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="dropdown m-1">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButtonAttemptees" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" disabled>
                                Attemptee
                            </button>
                            <div class="dropdown-menu" id="attemptees-list" aria-labelledby="dropdownMenuButtonAttemptees">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mt-4">
                            <div class="card shadow mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 id="ws-chart-title" class="text-primary font-weight-bold m-0">Overview</h6>
                                    <div class="dropdown no-arrow">
                                        <button class="btn btn-link btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false" type="button"><i class="fas fa-ellipsis-v text-gray-400"></i></button>
                                        <div class="dropdown-menu shadow dropdown-menu-right animated--fade-in" role="menu">
                                            <p class="text-center dropdown-header">Stat Type</p>
                                            <div id="wsaction-timetaken" class="dropdown-item active" role="presentation">&nbsp;Time taken</div>
                                            <div id="wsaction-flicked" class="dropdown-item" role="presentation">&nbsp;Flicked</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="ws-user-chart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-1">
                        <div class="col-md-12" id="topicwise-stats-holder">
                        </div>
                    </div>

                    <!-- Worksheet-specific data -->

                    <div class="row m-1">
                        <div class="dropdown m-1">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButtonWSQuestions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" <?php if (count($worksheets) == 0) echo "disabled"; ?>>
                                Question
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonWSQuestions" id="WSQuestionsList">
                            </div>
                        </div>
                    </div>

                    <div class="row m-1">
                        <div class="col-md-6" id="ws-q-card-holder"></div>
                        <div class="col-md-6" id="ws-q-att-card-holder">
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection