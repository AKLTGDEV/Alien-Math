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
            $("#subq").prop("disabled", true);
            var videos = "";
            (result.videos).forEach(v => {
                videos += `

                <video id="my-video" class="video-js" controls preload="auto" width="640" height="264" data-setup="{}">
                    <source src="${v['loc']}" type="video/mp4" />
                        <p class="vjs-no-js">
                            To view this video please enable JavaScript, and consider upgrading to a
                            web browser that
                            <a href="https://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
                        </p>
                </video>
                <button id="bookmark-saq-v-${v['id']}" qtype="SAQ" qid="${v['id']}" class="qbookmark btn btn-sm btn-primary">
                    Bookmark This Video
                </button>

                `;
            });
            
            $(".exp-holder").html(`

            Correct: <b>${result.correct}</b> <br>

            Explanation: <br>

            ${result.explanation} <br>

            ${videos}

            `);
        }
    });
}