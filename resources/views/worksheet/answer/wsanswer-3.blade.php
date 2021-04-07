@extends('layouts.app')

@section('content')


<script type="text/javascript" src="{{ config('app.url') }}/thirdparty/bs-charts.js"></script>
<script type="text/javascript" src="{{ config('app.url') }}/thirdparty/chart.min.js"></script>


<script>
    jQuery(document).ready(function() {
        $("title").text(`Test Results / {{ config('app.name', 'Crowdoubt') }}`);
        $("meta[property='og\\:title']").attr("content", `Test Results / {{ config('app.name', 'Crowdoubt') }}`);
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

        var ctx = document.getElementById('overview').getContext('2d');
        var overview_chart = new Chart(ctx, {
            "type": "bar",
            "data": {},
            "options": {
                "maintainAspectRatio": false,
                "legend": {
                    "display": false
                },
                "title": {}
            }
        })


        show_time_taken();


        function show_time_taken() {

            $.ajax({
                url: "{{ config('APP_URL') }}/stats/{{ $ws->id }}/{{ $self->username }}",
                method: 'get',
                data: {
                    _token: CSRF_TOKEN
                },
                success: function(result) {
                    $("#ws-chart-title").text("Overview - Time Taken");

                    var labels = [];
                    var rightwrong = [];
                    var netq = parseInt("{{ $ws->nos }}");

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

                    overview_chart.data = {
                        "labels": labels,
                        "datasets": [{
                            "label": "",
                            "backgroundColor": rightwrong,
                            "data": result.metrics.clock_hits
                        }]
                    };
                    overview_chart.update();
                }
            });
        }
    })
</script>


<div class="global-container">
    <div class="row mt-2">
        <div class="col-12 col-md-10 ml-md-auto mr-md-auto">
            <div class="card">
                <div class="card-body">
                    @if(!$fucked)
                    <div class="card-text text-center mt-1">
                        <h1 class="text-bold">{{$right}}/{{$total}}</h1>
                        <h4>Completed in {{$mins}} minutes</h4>

                        <a class="btn btn-sm btn-secondary mt-1 mb-2" href="{{ route('stats') }}">
                            Stats
                        </a>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card shadow mb-2">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 id="ws-chart-title" class="text-primary font-weight-bold m-0">
                                            Overview
                                        </h6>
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
                                            <canvas id="overview"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h5 class="text-secondary">Share Your Result:</h5>

                        <?php
                        $telegramURL = "https://telegram.me/share/url?url=" . urlencode(route('wsresult', [$shareid])) . "&text=Check+my+result+on+CrowDoubt!";
                        ?>

                        <!--<a class="btn btn-outline-primary" href="https://www.facebook.com/sharer/sharer.php?u={{ route('wsresult', [$shareid]) }}&display=popup">FB</a>-->
                        <div class="ml-1 fb-share-button" data-href="{{ route('wsresult', [$shareid]) }}" data-layout="button_count" data-size="large">
                            <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fdevelopers.facebook.com%2Fdocs%2Fplugins%2F&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">
                                Share
                            </a>
                        </div>
                        <a href="https://api.whatsapp.com/send?text={{ route('wsresult', [$shareid]) }} Check+my+result+on+CrowDoubt!" class="btn btn-outline-success target=" _blank">WhatsApp</a>
                        <a href="{{ $telegramURL }}" class="btn btn-outline-info" target="_blank">Telegram</a>
                    </div>
                    @else
                    <div class="card-text text-center mt-1">
                        <h1 class="text-bold">
                            Error occured during the test.
                        </h1>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-12">
                            <div class="fb-comments" data-href="{{Request::url()}}" data-numposts="5"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection