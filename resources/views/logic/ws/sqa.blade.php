function ans_submit_sqa(j, ans, clock_hits) {
    $.ajax({
        url:   `{{ config('app.url') }}/quiz/singleanswer/{{ $ws->slug }}/${j}`,
        method: 'post',
        data: {
            _token: CSRF_TOKEN,
            type: "SQA",
            answer: ans,
            hits: clock_hits
        },
        success: function(result) {
            $(".exp-holder").html(`

            Correct: <br>

            <ol>
                <li>${result.correct[0]}</li>
                <li>${result.correct[1]}</li>
                <li>${result.correct[2]}</li>
                <li>${result.correct[3]}</li>
            </ol>

            Explanation: <br>

            ${result.explanation}

            `);
        }
    });
}