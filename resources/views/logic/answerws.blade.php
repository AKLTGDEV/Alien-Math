<script>
    jQuery(document).ready(function($) {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        load_content(); // Load content the first time
        

        $("#refresh-cont-btn").click(function (e) {
            load_content();
        })


        clock_hits = [];
        for (let i = 0; i < {{$nos}}; i++) {
            clock_hits[i] = 0;
            
        }

        /*answers = [];
        for (let i = 0; i < {{$nos}}; i++) {
            answers[i] = "N";
            
        }

        opt_changes = [];
        for (let i = 0; i < {{$nos}}; i++) {
            opt_changes[i] = 0;
            
        }*/

        /*$(".option").click(function(e) {

            pid = $(this).attr("pid");
            opt = $(this).attr("opt");

            //Check if any of the other options are currently selected 

            if(answers[pid - 1] != "N"){
                opt_changes[pid-1]++;
                // Remove "selected" class from all other options
                for (let c = 1; c <= 4; c++) {
                    if(c == opt){
                        continue;
                    } else {
                        $("#opt_"+pid+"_"+c).removeClass("opt-selected");
                    }        
                }
                answers[pid - 1] = opt;
                $(this).addClass("opt-selected");
            } else {
                answers[pid - 1] = opt;
                $(this).addClass("opt-selected");
            }
        })*/

        function load_content() {
            /**
                PULL ALL THE DATA FROM THE SERVER AND PLACE THEM
                IN THE PLACES (BODIES AND OPTS)
            */

            <?php
use Illuminate\Support\Facades\Auth;
?>
            var logged_in = "{{ Auth::check() == true ? '1' : '0' }}";

            var pull_url = "{{ route('wsanswer-pc', [$ws->slug]) }} ";
            if (logged_in == 0) {
                pull_url = "{{ route('public-wsanswer-pc', [$ws->slug, $public_id]) }} ";
            }
        
            $.ajax({
                url: pull_url,
                method: 'get',
                data: {
                    _token: CSRF_TOKEN,
                },
                success: function(result) {
                    if(result.status == "error"){
                        $("#content-body").html(`

                        <div class="row mt-2 mb-2">
                            <div class="col-12">
                                <p class="text-center text-secondary">
                                    This Worksheet is either completed or is unavailable.
                                    You will now be redirected to the Stats Page.
                                </p>
                            </div>
                        </div>

                        `);
                        $("#clockdiv-holder").attr("style", "display: none;");

                        window.location.href = "{{ route('stats') }}";
                    } else {
                        // NEW CODE

                        var i = 1;
                        result.data.content.forEach(question => {

                            //STEP 1: Fill in the bodies
                            $("#question_content_"+i).html(question.body);

                            //STEP 2: Fill answer section
                            if(question.type == "MCQ"){
                                $("#answer-holder-"+i).html(`

                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="option" id="opt_${i}_1" pid="${i}" opt="1">
                                                        <div id="opt_${i}_1_body" class="option-text btn btn-outline-secondary shadow  btn-rounded waves-effect">
                                                            ${question.opts[0]}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="option" id="opt_${i}_2" pid="${i}" opt="2">
                                                        <div id="opt_${i}_2_body" class="option-text btn btn-outline-secondary shadow  btn-rounded waves-effect">
                                                        ${question.opts[1]}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="option" id="opt_${i}_3" pid="${i}" opt="3">
                                                        <div id="opt_${i}_3_body" class="option-text btn btn-outline-secondary shadow  btn-rounded waves-effect">
                                                        ${question.opts[2]}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="option" id="opt_${i}_4" pid="${i}" opt="4">
                                                        <div id="opt_${i}_4_body" class="option-text btn btn-outline-secondary shadow  btn-rounded waves-effect">
                                                        ${question.opts[3]}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                `);
                            } else if(question.type == "SAQ"){
                                $("#answer-holder-"+i).html(`--SAQ--`);
                            } else if(question.type == "SQA"){
                                $("#answer-holder-"+i).html(`--SQA--`);
                            }

                            i++;
                        });
                    }
                }
            });
        }

        /*function submit_A() {
            $("#sub").prop("disabled", true);
            //alert("Submitting the paper. Please wait");
            // Not needed

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });

            var logged_in = "{{ Auth::check() == true ? '1' : '0' }}";

            var sub_url = "{{ route('answerws') }} ";
            if (logged_in == 0) {
                sub_url = "{{ route('public-answerws', [$public_id]) }} ";
            }

            $.ajax({
                url: sub_url,
                method: 'post',
                data: {
                    _token: CSRF_TOKEN,
                    wsid: {{ $wsid }},
                    ans: JSON.stringify(answers),
                    clock_hits: JSON.stringify(clock_hits),
                    opt_changes: JSON.stringify(opt_changes)
                },
                success: function(result) {
                    console.log("RECIEVED: " + result);
                    if (result == "Y") {
                        var next_url = "{{ route('wsanswer-3', [$ws->slug]) }}";
                        if (logged_in == 0) {
                            next_url = "{{ route('public-wsanswer-3', [$ws->slug, $public_id]) }}";
                        }

                        window.location = next_url;
                    } else {
                        alert("Failed to Submit");
                    }
                }
            });
        }*/

        /*$("#sub").click(function(e) {
            //Make sure the user didn't actually mean to change the question.

            if(GLOB_SUB_WARNING_SHOWN){
                console.log("KNOB");
               submit_A();
            } else {
                alert("Clicking 'Submit' again will submit the Worksheet. If you meant to change the question, Please chick on the Question numbers on top of the page.")
                GLOB_SUB_WARNING_SHOWN = true;
            }
        })*/

        /*$("#ans-clear").click(function(e) {
            activeid = $("div .active")[1].id
            pid = activeid.split("body-q-")[1]

            answers[pid - 1] = "N"
            $("[pid=" + pid + "]").removeClass("opt-selected")
            console.log(JSON.stringify(answers));
        })*/

        var time_in_minutes = {{ $ws->mins }};
        var current_time = Date.parse(new Date());
        var deadline = new Date(current_time + time_in_minutes * 60 * 1000);


        function time_remaining(endtime) {
            var t = Date.parse(endtime) - Date.parse(new Date());
            var seconds = Math.floor((t / 1000) % 60);
            var minutes = Math.floor((t / 1000 / 60) % 60);
            var hours = Math.floor((t / (1000 * 60 * 60)) % 24);
            var days = Math.floor(t / (1000 * 60 * 60 * 24));
            return {
                'total': t,
                'days': days,
                'hours': hours,
                'minutes': minutes,
                'seconds': seconds
            };
        }

        function run_clock(id, endtime) {
            var clock = document.getElementById(id);

            function update_clock() {
                /**
                * This runs every second. Capture the currently active Q.
                */

                current = $(".active");
                if(current[0] != null){
                    current_pid = $(current[0]).attr("qid");
                    clock_hits[current_pid - 1]++;
                }

                var t = time_remaining(endtime);
                if(t.hours == 0 && t.minutes == 0 && t.seconds == 1){
                    submit_A();
                }
                //clock.innerHTML = 'minutes: '+t.minutes+'<br>seconds: '+t.seconds;
                clock.innerHTML = t.hours + ":" + t.minutes + ":" + t.seconds;
                if (t.total <= 0) {
                    clearInterval(timeinterval);
                }

                //console.log(clock_hits);
            }
            update_clock(); // run function once at first to avoid delay
            var timeinterval = setInterval(update_clock, 1000);
        }
        run_clock('clockdiv', deadline);
    })
</script>