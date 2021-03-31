@extends('layouts.app')

@section('content')


<script type="text/javascript" src="{{ config('app.url') }}/thirdparty/bs-charts.js"></script>
<script type="text/javascript" src="{{ config('app.url') }}/thirdparty/chart.min.js"></script>\

<script>
    jQuery(document).ready(function($) {
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
                url: `{{ config('APP_URL') }}/testseries/{{ $TS->encname }}/stats/${wsname}/att`,
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
                url: `{{ config('APP_URL') }}/testseries/{{ $TS->encname }}/stats/${GLOBAL_WSNAME}/u/${GLOBAL_UNAME}`,
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


<div class="container-fluid main">
    <div class="row mt-2">
        <div class="col-md-5">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header">
                            General Stats
                        </div>
                        <div class="card-body">
                            <h3 class="card-title text-center">
                                Test Series: {{ "$TS->name" }}
                            </h3>
                            <div class="card-text text-center mt-1">
                                <ul>
                                    <li>Attempt Rate: 0%</li>
                                    <li>Success Rate: 0%</li>
                                    <li>Time Spent: 0s</li>
                                    <li>Flick: 0</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="d-sm-flex justify-content-between align-items-center mb-4">
                        <h3 class="text-dark mb-0">Worksheets</h3>
                    </div>
                </div>

                <div class="col-12">
                    <div class="dropdown m-1">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButtonWorksheets" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" <?php

                                                                                                                                                                                            if (count($wslist) == 0) echo "disabled"; ?> style="width: 100%">
                            Worksheets
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonWorksheets">
                            @foreach($wslist as $ws)
                            <a wsname="{{$ws['name']}}" class="wsitem dropdown-item">{{ $ws['title'] }}</a>
                            @endforeach
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
            </div>
        </div>
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    Worksheets
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Attempt</th>
                                <th scope="col">Success</th>
                                <th scope="col">Time</th>
                                <th scope="col">Flick</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($wslist as $ws)
                            <tr>
                                <th scope="row">{{ $ws['title'] }}</th>
                                <td>{{ $ws['att_rate'] }}%</td>
                                <td>{{ $ws['success_rate'] }}%</td>
                                <td>{{ $ws['time'] }}s</td>
                                <td>{{ $ws['flick'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection