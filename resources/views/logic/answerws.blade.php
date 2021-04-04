<script>            
    jQuery(document).ready(function($) {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $("#current").val(1)

        load_content(1); // Load content the first time


        var mcq_answers = [];
        $('#answer-holder').on('click', '.option', function() {
            //console.log($(this).attr("opt"));
            mcq_answers.push($(this).attr("opt"));
            $(".option").removeClass("opt-selected");
            $(this).addClass("opt-selected");
        })


        $("#subq").click(function (e) {
            /**
            STEP 1: Submit Anser
            STEP 2: Display Correct answer and Hint
            STEP 3: display next answer
             */
            current = parseInt($("#current").val());
            current_type = $("#current-type").val();

            switch(current_type) {
                case "MCQ":
                    ans_submit_mcq(current, mcq_answers);
                    break;
                case "SAQ":
                    ans_submit_saq(current, $("#saq-answer").val());
                    break;
                case "SQA":
                    console.log("SQA")
                    break;
                default:
                    // report this incident
            }

            $("#nextq").prop("disabled", false);
        });

        $("#nextq").click(function (e) {
            current = parseInt($("#current").val());
            if(current >= parseInt("{{ $ws->nos }}")){
                console.log("DONE");
                //redirect to Stats Page
            } else {
                load_content(current+1);
                $("#current").val(current+1);
            }
        })


        clock_hits = [];
        for (let i = 0; i < {{$nos}}; i++) {
            clock_hits[i] = 0;
            
        }

        @include('logic.ws.saq')
        @include('logic.ws.mcq')
        @include('logic.ws.sqa')

        function load_content(j) {
            /**
             * Clear the exploanation area first
             * 
             */
            $(".exp-holder").empty();

            /**
             * Now disable the "Next question" button
             * 
             */
            $("#nextq").prop("disabled", true);
            
            $.ajax({
                url:   `{{ config('app.url') }}/quiz/pullcontent/{{ $ws->slug }}/${j}`,
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

                        //console.log(result.data);
                        question = result.data;
                        $("#current-type").val(question.type);

                        $("#question_content").html(question.body);

                        if(question.type == "SAQ"){
                                $("#answer-holder").html(`
                                
                                <input placeholder="Answer Here" class="form-control form-input" type="text" id="saq-answer">
                                
                                `);
                        } else if(question.type == "SQA"){
                                $("#answer-holder").html(`

                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="sqa-select-1">
                                            ${question.opts[0]}
                                        </label>
                                    </div>
                                    <select class="custom-select" id="sqa-select-1">
                                        <option selected>Select</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                        <option value="4">Four</option>
                                    </select>
                                </div>

                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="sqa-select-2">
                                            ${question.opts[1]}
                                        </label>
                                    </div>
                                    <select class="custom-select" id="sqa-select-2">
                                        <option selected>Select</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                        <option value="4">Four</option>
                                    </select>
                                </div>

                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="sqa-select-3">
                                            ${question.opts[2]}
                                        </label>
                                    </div>
                                    <select class="custom-select" id="sqa-select-3">
                                        <option selected>Select</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                        <option value="4">Four</option>
                                    </select>
                                </div>

                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="sqa-select-4">
                                            ${question.opts[3]}
                                        </label>
                                    </div>
                                    <select class="custom-select" id="sqa-select-4">
                                        <option selected>Select</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                        <option value="4">Four</option>
                                    </select>
                                </div>

                                `);
                            } else if(question.type == "MCQ"){
                                $("#answer-holder").html(`

                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="option" id="opt_1" pid="${j}" opt="1">
                                                        <div id="opt_1_body" class="option-text btn btn-outline-secondary shadow  btn-rounded waves-effect">
                                                            ${question.opts[0]}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="option" id="opt_2" pid="${j}" opt="2">
                                                        <div id="opt_2_body" class="option-text btn btn-outline-secondary shadow  btn-rounded waves-effect">
                                                        ${question.opts[1]}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="option" id="opt_3" pid="${j}" opt="3">
                                                        <div id="opt_3_body" class="option-text btn btn-outline-secondary shadow  btn-rounded waves-effect">
                                                        ${question.opts[2]}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="option" id="opt_4" pid="${j}" opt="4">
                                                        <div id="opt_4_body" class="option-text btn btn-outline-secondary shadow  btn-rounded waves-effect">
                                                        ${question.opts[3]}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                `);
                            }
                    }
                }
            });
        }

        /*var time_in_minutes = {{ $ws->mins }};
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
        }*/

        /*function run_clock(id, endtime) {
            var clock = document.getElementById(id);

            function update_clock() {
                //This runs every second. Capture the currently active Q.

                current = $(".active");
                if(current[0] != null){
                    current_pid = $(current[0]).attr("qid");
                    clock_hits[current_pid - 1]++;
                }

                var t = time_remaining(endtime);
                if(t.hours == 0 && t.minutes == 0 && t.seconds == 1){
                    //submit_A();
                }
                //clock.innerHTML = 'minutes: '+t.minutes+'<br>seconds: '+t.seconds;
                //clock.innerHTML = t.hours + ":" + t.minutes + ":" + t.seconds;
                //if (t.total <= 0) {
                //    clearInterval(timeinterval);
                //}

                console.log(clock_hits);
            }
            update_clock(); // run function once at first to avoid delay
            var timeinterval = setInterval(update_clock, 1000);
        }
        run_clock('clockdiv', deadline);*/
    })
</script>
