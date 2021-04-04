function ans_submit_mcq(j, ans, clock_hits) {
    $.ajax({
        url: `{{ config('app.url') }}/quiz/singleanswer/{{ $ws->slug }}/${j}`,
        method: 'post',
        data: {
            _token: CSRF_TOKEN,
            type: "MCQ",
            answer: ans,
            hits: clock_hits,
        },
        success: function (result) {            
            $(".exp-holder").html(`

            Correct: <b>${result.correct}</b> <br>

            Explanation: <br>

            ${result.explanation}

            `);
        }
    });
}