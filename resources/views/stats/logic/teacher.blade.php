<script>
    jQuery(document).ready(function($) {
        var GLOBAL_UNAME = "";
        var GLOBAL_WSID = "";
        var GLOBAL_QNO = 0;

        var worksheets = <?php echo json_encode($worksheets) ?>;

        var tags_ask = JSON.parse('<?php
                                    if (count($tags_posted) > 4) {
                                        echo json_encode(array_slice($tags_posted, 0, 4));
                                    } else {
                                        echo json_encode($tags_posted);
                                    }
                                    ?>');

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




        var ctx = document.getElementById('topics-chart').getContext('2d');
        var topicschart = new Chart(ctx, {
            "type": "doughnut",
            "data": {},
            "options": {
                "maintainAspectRatio": false,
                "legend": {
                    "display": false
                },
                "title": {}
            }
        })

        update_topics_chart("posted");

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
                url: "{{ config('APP_URL') }}/stats/" + wsid + "/att",
                method: 'get',
                data: {
                    _token: CSRF_TOKEN
                },
                success: function(result) {
                    result.forEach(att => {
                        username = att[0];
                        name = att[1];
                        $("#attemptees-list").append('<a wsid="' + wsid + '" uname="' + username + '" class="useritem dropdown-item">' + name + '</a>');
                    });
                }
            });
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
                    var topics = result.topics;

                    var tabs = ``;
                    var bodies = ``;

                    count = 1;
                    for (key in topics) {
                        if (topics.hasOwnProperty(key)) {
                            topic_data = topics[key];

                            //console.log(topic_data.right)

                            tabs += `
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#body-${count}">${key}</a>
                            </li>
                            `;

                            bodies += `
                            <div class="tab-pane container" id="body-${count}">
                                <h4 class="mt-1">${topic_data.right}% of questions attempted correctly</h4>
                                <h4>${topic_data.left}% of questions left</h4>
                            </div>
                            `;

                            count++;
                        }
                    }

                    $("#topicwise-stats-holder").empty();
                    $("#topicwise-stats-holder").html(`

                            <div class="card">
                                <div class="card-header">
                                    Topic-wise breakdown: @${GLOBAL_UNAME}
                                </div>

                                <div class="card-body">
                                    <ul class="nav nav-tabs">
                                        ${tabs}
                                    </ul>

                                    <!-- Tab panes -->
                                    <div class="tab-content">
                                        ${bodies}
                                    </div>
                                </div>
                            </div>

                    `);
                }
            });
        }

        $("#attemptees-list").on('click', '.useritem', function(e) {
            GLOBAL_UNAME = $(this).attr("uname");
            GLOBAL_WSID = $(this).attr("wsid");

            $("#active-user-head").text("@" + GLOBAL_UNAME);

            update_ws_user_question(GLOBAL_WSID, GLOBAL_QNO, GLOBAL_UNAME);

            wschart_update("timetaken");
        })

        $("#topics-posted").click(function(e) {
            $("#topics-answered").removeClass("active");
            $("#topics-posted").addClass("active");

            update_topics_chart("posted");
        })

        $("#topics-answered").click(function(e) {
            $("#topics-posted").removeClass("active");
            $("#topics-answered").addClass("active");

            update_topics_chart("answered");
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

        function update_topics_chart(type) {
            if (type == "posted") {
                $("#topics-heading").text("Topics - Posted")

                topicschart.data = {
                    "labels": Object.keys(tags_ask),
                    "datasets": [{
                        "label": "",
                        "backgroundColor": rand_color(Object.keys(tags_ask).length),
                        "data": Object.values(tags_ask)
                    }]
                };
                topicschart.update();

            } else {
                $("#topics-heading").text("Topics - Answered")

                topicschart.data = {
                    "labels": Object.keys(tags_ans),
                    "datasets": [{
                        "label": "",
                        "backgroundColor": rand_color(Object.keys(tags_ans).length),
                        "data": Object.values(tags_ans)
                    }]
                };

                topicschart.update();
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