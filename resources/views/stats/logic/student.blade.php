<script>
    jQuery(document).ready(function($) {
        var GLOBAL_UNAME = "{{ $user->username }}";
        var GLOBAL_WSID = "";
        var GLOBAL_QNO = 0;

        var worksheets = <?php echo json_encode($worksheets) ?>;

        var dr = JSON.parse('<?php
                                echo json_encode($daily_record);
                                ?>');
        var dr_ctx = document.getElementById('dr_chart').getContext('2d');
        var dr_chart = new Chart(dr_ctx, {
            "type": "line",
            "data": {
                "labels": Object.keys(dr),
                "datasets": [{
                    "label": "Rating",

                    "data": Object.values(dr)
                }]
            },
            "options": {
                "maintainAspectRatio": false,
                "legend": {
                    "display": false
                },
                "title": {}
            }
        })

        var ctx2 = document.getElementById('ws-user-chart').getContext('2d');
        var ws_user_chart = new Chart(ctx2, {
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

        $(".wsitem").click(function(e) {
            wsid = $(this).attr("wsid");

            curr_worksheet = null;
            worksheets.forEach(ws => {
                if (ws.id == wsid) {
                    curr_worksheet = ws;
                }
            });

            $("#active-user-head").empty();
            $("#active-user-question").empty();
            $("#active-ws-head").text(curr_worksheet.title);

            $('#WSQuestionsList').empty();

            for (let i = 1; i <= curr_worksheet.nos; i++) {
                $("#WSQuestionsList").append(`
                <a wsid="${curr_worksheet.id}" q="${i}" class="ws-q-item dropdown-item">Question ${i}</a>
                `);
            }

            $("#dropdownMenuButtonWorksheets").text($(this).text())

            GLOBAL_WSID = wsid;
            $("#active-user-head").text("@" + GLOBAL_UNAME);
            update_ws_user_question(GLOBAL_WSID, GLOBAL_QNO, GLOBAL_UNAME);
            wschart_update("timetaken");
        });

        $('#WSQuestionsList').on('click', '.ws-q-item', function() {

            var wsid = $(this).attr("wsid");
            var q = $(this).attr("q");

            GLOBAL_QNO = q;
            update_ws_user_question(GLOBAL_WSID, GLOBAL_QNO, GLOBAL_UNAME);

            $("#active-user-question").text("#" + q);

            $.ajax({
                url: `{{ config('APP_URL') }}/stats/${wsid}/q/${q}`,
                method: 'get',
                data: {
                    _token: $('meta[name="_token"]').attr('content')
                },
                success: function(result) {
                    $("#ws-q-card-holder").empty();

                    var topics_text = "";

                    (result.topics).forEach(topic => {
                        topics_text += `<span class="mx-1 badge badge-pill badge-success">${topic.name}</span>`;
                    });

                    $("#ws-q-card-holder").html(`
                        <div class="card">
                            <div class="card-header">
                                Question ${q}
                            </div>
                            <div class="card-body">
                                <p>
                                    <h4>${result.correct}% of attemptees got it right</h4>
                                    <h4>${result.left}% of attemptees left it</h4>
                                    <h4>Average Attempt time is ${result.hits} seconds</h4>
                                </p>

                                <div class="row">
                                    ${topics_text}
                                </div>
                            </div>
                        </div>

                    `);
                }
            });
        });

        function wschart_update(type) {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $("#dropdownMenuButtonAttemptees").text(GLOBAL_UNAME);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ config('APP_URL') }}/stats/" + GLOBAL_WSID + "/" + GLOBAL_UNAME,
                method: 'get',
                data: {
                    _token: CSRF_TOKEN
                },
                success: function(result) {
                    if (type == "timetaken") {
                        ws_time_taken(result);
                    } else {
                        ws_flicked(result);
                    }
                }
            });
        }

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

        function rand_color(size) {
            ret = [];
            for (let j = 0; j < size; j++) {
                var letters = "0123456789ABCDEF";
                var color = '#';
                for (var i = 0; i < 6; i++)
                    color += letters[(Math.floor(Math.random() * 16))];

                ret.push(color);
            }

            return ret;
        }

        function update_ws_user_question(ws, q, username) {
            $("#ws-q-att-card-holder").empty();

            if (q != 0) {
                // Pull the info
                $.ajax({
                    url: `{{ config('APP_URL') }}/stats/${ws}/q/${q}/u/${username}`,
                    method: 'get',
                    data: {
                        _token: $('meta[name="_token"]').attr('content')
                    },
                    success: function(result) {
                        $("#ws-q-att-card-holder").html(`
                        <div class="card">
                            <div class="card-header">
                                ${"@"+username} :: Question ${q}
                            </div>
                            <div class="card-body">
                                <ul>
                                    <li>${result.qstatus}</li>
                                    <li>${"@"+username} was faster than <b>${result.hits_lower}%</b> of attemptees</li>

                                    <li>Topics-related stats here</li>
                                </ul>
                            </div>
                        </div>
                        `);
                    }
                });
            }
        }



        function ws_time_taken(result) {
            $("#ws-chart-title").text("Overview - Time Taken");

            var labels = [];
            var rightwrong = [];
            var netq = parseInt(result.general.nos);

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
                    "data": result.metrics.clock_hits
                }]
            };
            ws_user_chart.update();
        }

        function ws_flicked(result) {
            $("#ws-chart-title").text("Overview - Flicked");

            var labels = [];
            var rightwrong = [];
            var netq = parseInt(result.general.nos);

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