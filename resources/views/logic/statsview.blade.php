<script>
    var GLOBAL_POST_TITLE = "--";
    var GLOBAL_POST_ATT = "--";
    var GLOBAL_POST_STYPE = "--";

    var GLOBAL_ATT_LIST = [];
    <?php for ($i = 1; $i <= count($titles); $i++) { ?>
        var att_{{ $i }}_json = <?php echo json_encode($attemptees[$i - 1]) ?>;
        att_{{ $i }}_json.unshift("Everyone");
        GLOBAL_ATT_LIST.push(att_{{ $i }}_json);
    <?php } ?>

    var post_titles = <?php echo json_encode($titles) ?>;

    <?php

    use App\worksheets; ?>
    var post_wsid_list = [];
    <?php for ($i = 1; $i <= count($titles); $i++) { ?>
        post_wsid_list[{{ $i-1 }}] = {{ worksheets::getwsid_by_title($titles[$i-1]) }}
    <?php } ?>

    function titlechange(title) {
        if (title == 0) {
            $(".titles-dd-head").text("All Posts")
            GLOBAL_POST_TITLE = 0;
            $("#stats_for_btn").prop("disabled", true);
            return;
        } else {
            $(".titles-dd-head").text(post_titles[title - 1])
            GLOBAL_POST_TITLE = title;
            $("#stats_for_btn").prop("disabled", false);
        }

        // Refresh attemptees list,
        current_att_list = GLOBAL_ATT_LIST[title - 1];
        $("#att_list").empty();
        current_att_list.forEach(att => {
            $("#att_list").append("<li><a class='dropdown-item att-item' att='" + att + "' href='#'>" + att + "</a></li>")
        });
    }

    function attchange(att) {
        $("#sf-head").text(att)
        GLOBAL_POST_ATT = att;
    }

    function stype_change(stype) {
        if (stype == 1) {
            $(".stat-type").text("Net time")
        } else if (stype == 2) {
            $(".stat-type").text("Changed")
        } else if (stype == 3) {
            $(".stat-type").text("Flicked")
        }

        GLOBAL_POST_STYPE = stype;
    }

    function update_pbar(right, wrong, left) {
        if(GLOBAL_POST_TITLE != "--" && 
           GLOBAL_POST_ATT != "--" &&
           GLOBAL_POST_STYPE != "--"){
            $("#rwl_bar").removeClass("hidden");
            net = right + wrong + left;

            right_percentage = (right / net) * 100;
            wrong_percentage = (wrong / net) * 100;
            left_percentage = (left / net) * 100;

            $("#pb-right").css("width", right_percentage + "%");
            $("#pb-wrong").css("width", wrong_percentage + "%");
            $("#pb-left").css("width", left_percentage + "%");

           }
    }

    function stat_chart(metrics, results) {
        console.log(metrics);
        labels_Q = []
        for (let i = 1; i <= net; i++) {
            labels_Q[i - 1] = i;

        }
        series=[];
        i=0;
        results.forEach(item => {
            if(item == "T"){
                meta = 'correct_ans'
            } else if(item == "F"){
                meta = 'wrong_ans'
            } else {
                meta = 'left_ans'
            }
            series.push({
                meta: meta,
                value: metrics[i],
            })

            i++;
        });

        var data = {
            labels: labels_Q,
            series: [series]
        };
        var options = {
            axisX: {
                labelInterpolationFnc: function(value) {
                    return 'Q' + value;
                }
            }
        };

        new Chartist.Bar('#stats_chart', data, options);
    }

    function update_graphics(canvas) {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });

        if (GLOBAL_POST_ATT == "Everyone") {
            $.ajax({
                url: "{{ config('APP_URL') }}/stats/" + post_wsid_list[GLOBAL_POST_TITLE - 1] + "/",
                method: 'get',
                data: {
                    _token: CSRF_TOKEN
                },
                success: function(result) {
                    update_pbar(result.general.right, result.general.wrong, result.general.left)
                }
            });
        } else {
            /**
             * STEP1: Get all the stats for user+WS.
             */
            $.ajax({
                url: "{{ config('APP_URL') }}/stats/" + post_wsid_list[GLOBAL_POST_TITLE - 1] + "/" + GLOBAL_POST_ATT,
                method: 'get',
                data: {
                    _token: CSRF_TOKEN
                },
                success: function(result) {
                    net = result.general.right + result.general.wrong + result.general.left
                    $("#score").text(result.general.right+"/"+net);
                    update_pbar(result.general.right, result.general.wrong, result.general.left)
                    if (GLOBAL_POST_STYPE == 1) {
                        stat_chart(result.metrics[0], result.results)
                    }
                    if (GLOBAL_POST_STYPE == 2) {
                        stat_chart(result.metrics[1], result.results)
                    }
                }
            });
        }
    }
</script>

<style>

@namespace ct "http://gionkunz.github.com/chartist-js/ct";
.ct-bar[ct|meta="correct_ans"] {
    stroke: #18e35e !important;
}

.ct-bar[ct|meta="wrong_ans"] {
    stroke: #ff3203 !important;
}

.ct-bar[ct|meta="left_ans"] {
    stroke: #9a97a6 !important;
}
</style>