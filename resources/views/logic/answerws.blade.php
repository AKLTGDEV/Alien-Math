<script>
    jQuery(document).ready(function($) {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $("#current").val(1)
        var clock_hits = 0;

        var _clock_ = window.setInterval(function() {
            clock_hits++;
        }, 1000);

        load_content(1); // Load content the first time

        $('#report-modal').on('show.bs.modal', function() {
            $("#report-qn").text($("#current").val());
        })

        $("#report-submit").click(function(e) {
            var rep_data = {};

            rep_data['_token'] = CSRF_TOKEN;
            rep_data['id'] = $("#current").val();
            rep_data['type'] = $("#current-type").val();
            rep_data['body'] = $("#report-body").val().trim();

            $.ajax({
                url: `{{ config('app.url') }}//report/ws/{{ $ws->slug }}/submit`,
                method: 'get',
                data: rep_data,
                success: function(result) {
                    if (result.status) {
                        $("#report-modal").modal('hide');
                    }
                }
            })

        })

        const body_editor = SUNEDITOR.create((document.getElementById('report-body') || 'report-body'), {
            minWidth: "100%",
        });
        body_editor.onChange = (contents, core) => {
            body_editor.save();
        }


        var mcq_answers = [];
        $('#answer-holder').on('click', '.option', function() {
            //console.log($(this).attr("opt"));
            mcq_answers.push($(this).attr("opt"));
            $(".option").removeClass("opt-selected");
            $(this).addClass("opt-selected");
        })


        $("#subq").click(function(e) {
            /**
            STEP 1: Submit Anser
            STEP 2: Display Correct answer and Hint
            STEP 3: display next answer
             */
            current = parseInt($("#current").val());
            current_type = $("#current-type").val();

            switch (current_type) {
                case "MCQ":
                    ans_submit_mcq(current, mcq_answers, clock_hits);
                    break;
                case "SAQ":
                    ans_submit_saq(current, $("#saq-answer").val(), clock_hits);
                    break;
                case "SQA":
                    var a1 = $('#sqa-select-1').find(":selected").attr("value");
                    var a2 = $('#sqa-select-2').find(":selected").attr("value");
                    var a3 = $('#sqa-select-3').find(":selected").attr("value");
                    var a4 = $('#sqa-select-4').find(":selected").attr("value");

                    a1_text = $('#sqa-select-1-text').text().trim();
                    a2_text = $('#sqa-select-2-text').text().trim();
                    a3_text = $('#sqa-select-3-text').text().trim();
                    a4_text = $('#sqa-select-4-text').text().trim();


                    var sqa_answers = {};
                    sqa_answers[a1_text] = a1;
                    sqa_answers[a2_text] = a2;
                    sqa_answers[a3_text] = a3;
                    sqa_answers[a4_text] = a4;

                    ans_submit_sqa(current, sqa_answers, clock_hits);
                    break;
                default:
                    // report this incident
            }

            $("#nextq").prop("disabled", false);
        });

        $("#nextq").click(function(e) {
            clock_hits = 0;
            current = parseInt($("#current").val());
            if (current >= parseInt("{{ $ws->nos }}")) {
                console.log("DONE");
                window.location = `{{ config('app.url') }}/worksheets/done/{{ $ws->slug }}`;
            } else {
                load_content(current + 1);
                $("#current").val(current + 1);
            }
        })

        @include('logic.ws.saq') @include('logic.ws.mcq') @include('logic.ws.sqa')

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
                url: `{{ config('app.url') }}/quiz/pullcontent/{{ $ws->slug }}/${j}`,
                method: 'get',
                data: {
                    _token: CSRF_TOKEN,
                },
                success: function(result) {
                    $("#subq").prop("disabled", false);

                    if (result.status == "error") {
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

                        question = result.data;
                        $("#current-type").val(question.type);
                        $("#current-id").val(question.id);

                        $("#question_content").html(question.body);

                        if (question.type == "SAQ") {
                            $("#answer-holder").html(`
                                
                                <input placeholder="Answer Here" class="form-control form-input" type="text" id="saq-answer">
                                
                                `);
                        } else if (question.type == "SQA") {
                            $("#answer-holder").html(`

                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="sqa-select-1" id="sqa-select-1-text">
                                            ${question.opts[0]}
                                        </label>
                                    </div>
                                    <select class="custom-select" id="sqa-select-1">
                                        <option value="0" selected>Select</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                        <option value="4">Four</option>
                                    </select>
                                </div>

                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="sqa-select-2" id="sqa-select-2-text">
                                            ${question.opts[1]}
                                        </label>
                                    </div>
                                    <select class="custom-select" id="sqa-select-2">
                                        <option value="0" selected>Select</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                        <option value="4">Four</option>
                                    </select>
                                </div>

                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="sqa-select-3" id="sqa-select-3-text">
                                            ${question.opts[2]}
                                        </label>
                                    </div>
                                    <select class="custom-select" id="sqa-select-3">
                                        <option value="0" selected>Select</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                        <option value="4">Four</option>
                                    </select>
                                </div>

                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="sqa-select-4" id="sqa-select-4-text">
                                            ${question.opts[3]}
                                        </label>
                                    </div>
                                    <select class="custom-select" id="sqa-select-4">
                                        <option value="0" selected>Select</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                        <option value="4">Four</option>
                                    </select>
                                </div>

                                `);
                        } else if (question.type == "MCQ") {
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
    })
</script>