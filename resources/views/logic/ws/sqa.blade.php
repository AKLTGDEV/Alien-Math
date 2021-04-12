function ans_submit_sqa(j, ans, clock_hits) {
    $.ajax({
        url: `{{ config('app.url') }}/quiz/singleanswer/{{ $ws->slug }}/${j}`,
        method: 'post',
        data: {
            _token: CSRF_TOKEN,
            type: "SQA",
            answer: ans,
            hits: clock_hits
        },
        success: function (result) {
            var videos = "";

            (result.videos).forEach(v => {
                videos += `

                <video id="my-video" class="video-js" controls preload="auto" width="640" height="264" data-setup="{}">
                    <source src="${v}" type="video/mp4" />
                        <p class="vjs-no-js">
                            To view this video please enable JavaScript, and consider upgrading to a
                            web browser that
                            <a href="https://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
                        </p>
                </video>

                `;
            });

            $(".exp-holder").html(`

            Correct: <br>

            <ol>
                <li>${result.correct[0]}</li>
                <li>${result.correct[1]}</li>
                <li>${result.correct[2]}</li>
                <li>${result.correct[3]}</li>
            </ol>

            Explanation: <br>

            ${result.explanation} <br>

            ${videos}

            `);
        }
    });
}