function ans_submit_saq(j, ans, clock_hits) {
    $.ajax({
        url:   `{{ config('app.url') }}/quiz/singleanswer/{{ $ws->slug }}/${j}`,
        method: 'post',
        data: {
            _token: CSRF_TOKEN,
            type: "SAQ",
            answer: ans,
            hits: clock_hits,
        },
        success: function(result) {
            $(".exp-holder").html(`

            Correct: <b>${result.correct}</b> <br>

            Explanation: <br>

            ${result.explanation}

            `);
        }
    });
}