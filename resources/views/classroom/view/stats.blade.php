@extends('layouts.app')

@section('content')


<!-- Class NAV -->
<nav class="navbar navbar-expand navbar-light bg-light">
    <div class="d-flex">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsingNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
    <div class="navbar-collapse collapse justify-content-center" id="collapsingNavbar">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('viewclassroom', [$class->id]) }}">Class</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('viewclassroomtimeline', [$class->id]) }}">Timeline</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('class_stats', [$class->id]) }}">Stats</a>
            </li>
        </ul>
    </div>
</nav>

<script type="text/javascript" src="{{ config('app.url') }}/thirdparty/bs-charts.js"></script>
<script type="text/javascript" src="{{ config('app.url') }}/thirdparty/chart.min.js"></script>\

<script>
    jQuery(document).ready(function($) {

        $("#new-collection").click(function(e) {
            $("#newCollectionModal").modal('show');
        })

        var GLOBAL_UNAME = "";
        var GLOBAL_WSNAME = "";

        var ctx2 = document.getElementById('ws-user-chart').getContext('2d');
        var ws_user_chart = new Chart(ctx2, {
            "type": "bar",
            "data": {},
            "options": {
                "maintainAspectRatio": false,
                "scales": {
                    "yAxes": [{
                        "ticks": {
                            "beginAtZero": true
                        }
                    }]
                },
                "legend": {
                    "display": false
                },
                "title": {}
            }
        })

        $(".wsitem").click(function(e) {
            wsname = $(this).attr("wsname");
            $("#dropdownMenuButtonWorksheets").text($(this).text())
            $("#dropdownMenuButtonAttemptees").prop("disabled", false);

            //We have the wsid. Get the list of attemptees from server and populate the entries.
            $("#attemptees-list").empty();
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ config('APP_URL') }}/class/{{ $cid }}" + "/stats/" + wsname + "/att",
                method: 'get',
                data: {
                    _token: CSRF_TOKEN
                },
                success: function(result) {
                    result.forEach(att => {
                        username = att['username'];
                        name = att['name'];
                        $("#attemptees-list").append('<a wsname="' + wsname + '" uname="' + username + '" class="useritem dropdown-item">' + name + '</a>');
                    });
                }
            });
        });

        function wschart_update(type) {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ config('APP_URL') }}/class/{{ $cid }}" + "/stats/" + GLOBAL_WSNAME + "/u/" + GLOBAL_UNAME,
                method: 'get',
                data: {
                    _token: CSRF_TOKEN
                },
                success: function(result) {
                    var att_body = JSON.parse(result.attempt.body);
                    if (type == "timetaken") {
                        ws_time_taken(result);
                    } else {
                        ws_flicked(result);
                    }

                    $("#att_name_holder").text("Stats for @" + GLOBAL_UNAME);

                    var netq = result.general.right + result.general.wrong + result.general.left;
                    $("#icard").html(`

                    <div class="card shadow">
                        <div class="card-body">
                            <h3 class="card-title text-center">
                                Stats for "` + result.general.wsinfo.title + `"
                            </h3>
                            <div class="card-text text-center mt-1">
                                <h1 class="text-bold">
                                    ` + result.general.right + `/` + netq + `
                                </h1>
                                <h4>Completed in ` + Math.round(att_body.nettime / 60, 3) + ` minutes</h4>
                            </div>
                        </div>
                    </div>
                    `)
                }
            });
        }


        $("#attemptees-list").on('click', '.useritem', function(e) {
            GLOBAL_UNAME = $(this).attr("uname");
            GLOBAL_WSNAME = $(this).attr("wsname");

            wschart_update("timetaken");
        })

        $("#wsaction-timetaken").click(function(e) {
            $("#wsaction-flicked").removeClass("active");
            $("#wsaction-timetaken").addClass("active");

            wschart_update("timetaken")
        })

        $("#wsaction-flicked").click(function(e) {
            $("#wsaction-timetaken").removeClass("active");
            $("#wsaction-flicked").addClass("active");

            wschart_update("flicked")
        })

        function ws_time_taken(result) {
            $("#ws-chart-title").text("Overview - Time Taken");

            var labels = [];
            var rightwrong = [];
            var netq = result.general.right + result.general.wrong + result.general.left;

            for (let k = 1; k <= netq; k++) {
                labels.push("Q" + k);

                if (result.results[k - 1] == "F") {
                    rightwrong.push("#fc6203")
                } else if (result.results[k - 1] == "T") {
                    rightwrong.push("#05f77e")
                } else {
                    rightwrong.push("#a6aba2")
                }
            }

            ws_user_chart.data = {
                "labels": labels,
                "datasets": [{
                    "label": "",
                    "backgroundColor": rightwrong,
                    "data": result.metrics[0]
                }]
            };
            ws_user_chart.update();
        }

        function ws_flicked(result) {
            $("#ws-chart-title").text("Overview - Flicked");

            var labels = [];
            var rightwrong = [];
            var netq = result.general.right + result.general.wrong + result.general.left;

            for (let k = 1; k <= netq; k++) {
                labels.push("Q" + k);

                if (result.results[k - 1] == "F") {
                    rightwrong.push("#fc6203")
                } else if (result.results[k - 1] == "T") {
                    rightwrong.push("#05f77e")
                } else {
                    rightwrong.push("#a6aba2")
                }
            }

            ws_user_chart.data = {
                "labels": labels,
                "datasets": [{
                    "label": "",
                    "backgroundColor": rightwrong,
                    "data": result.metrics[1]
                }]
            };
            ws_user_chart.update();
        }
    })
</script>


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
                    <div class="col-md-6 col-xl-4 mb-4">
                        <div class="card shadow border-left-primary py-2">
                            <div class="card-body">
                                <div class="row align-items-center no-gutters">
                                    <div class="col">
                                        <div class="text-uppercase text-primary font-weight-bold text-xs mb-1">
                                            <span>
                                                Posted
                                            </span>
                                        </div>
                                        <div class="text-dark font-weight-bold h5 mb-0">
                                            <span>{{$nos_q_ws}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4 mb-4">
                        <div class="card shadow border-left-success py-2">
                            <div class="card-body">
                                <div class="row align-items-center no-gutters">
                                    <div class="col ">
                                        <div class="text-uppercase text-success font-weight-bold text-xs mb-1">
                                            <span>
                                                Answered
                                            </span>
                                        </div>
                                        <div class="text-dark font-weight-bold h5 mb-0">
                                            <span>{{ $nos_q_ws_ans }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4 mb-4">
                        <div class="card shadow border-left-warning py-2">
                            <div class="card-body">
                                <div class="row align-items-center no-gutters">
                                    <div class="col ">
                                        <div class="text-uppercase text-warning font-weight-bold text-xs mb-1">
                                            <span>Members</span>
                                        </div>
                                        <div class="text-dark font-weight-bold h5 mb-0">
                                            <span>{{ $class->users }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!--ROW-->

                    <div class="col-12">
                        <div class="d-sm-flex justify-content-between align-items-center mb-4">
                            <h3 class="text-dark mb-0">Worksheets</h3>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="dropdown m-1">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButtonWorksheets" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" <?php

                                                                                                                                                                                                if (count($worksheets) == 0) echo "disabled"; ?> style="width: 100%">
                                Worksheets
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonWorksheets">
                                <?php foreach ($worksheets as $ws) { ?>
                                    <a wsname="{{$ws['name']}}" class="wsitem dropdown-item">{{ $ws['title'] }}</a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="dropdown m-1">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButtonAttemptees" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" disabled style="width: 100%">
                                Attemptee
                            </button>
                            <div class="dropdown-menu" id="attemptees-list" aria-labelledby="dropdownMenuButtonAttemptees">
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <h4 class="text-dark mt-2" id="att_name_holder">
                        </h4>
                    </div>

                    <div class="col-12" id="icard">
                    </div>

                    <div class="col-12 mt-4">
                        <div class="row">
                            <div class="col-md-12">
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
                    </div>

                    @if ($isadmin)
                    <div class="col-12">
                        <div class="d-sm-flex justify-content-between align-items-center mb-4">
                            <h3 class="text-dark mb-0">Collections</h3>
                            <div id="new-collection" class="btn btn-primary btn-sm d-none d-sm-inline-block" role="button">
                                <i class="fas fa-plus fa-sm text-white-50"></i>
                                &nbsp;New
                            </div>
                        </div>
                    </div>

                    @if (count($collections) > 0)
                    <div class="col-12 mb-2">
                        <div class="container-fluid">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <!-- REPEAT THIS -->
                                        @foreach ($collections as $c)
                                        <div class="col px-2 py-2">
                                            <a href="{{ route('class_viewcollection', [$cid, $c->encname]) }}" class="btn btn-lg btn-outline-secondary">
                                                {{ $c->name }}
                                            </a>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="col-12 mb-2">
                        <p>
                            You can assign multiple collections to each question of a Worksheet. Use Collection to track performance of your students.
                        </p>
                    </div>

                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<?php

use Illuminate\Support\Facades\Input;
?>

<div class="modal" tabindex="-1" role="dialog" id="newCollectionModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('class_newcollection', [$class->id]) }}" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Create Collection</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input placeholder="Name" class="form-control form-input" type="text" value="{{ Input::old('name') }}" name="name" id="nc-name">
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


@endsection