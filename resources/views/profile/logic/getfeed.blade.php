@include('includes.wsitem_body')
@include('includes.postitem_body')

<script>
    jQuery(document).ready(function($) {
        $("title").text(`{{ $user->name }} {{ "(@".$user->username.")" }} / {{ config('app.name', 'Crowdoubt') }}`);

        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var feed_idx = [];

        remote(null);

        $("#feed_holder").on('click', '.option', function(e) {
            var PRETEXT = $(this).text();
            $(this).html("<i class='fas fa-2x fa-spinner fa-spin'></i>")

            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });

            __el = $(this)
            __pid = $(this).attr("pid")
            __opt = $(this).attr("opt")

            $.ajax({
                url: "{{ route('answerpost') }}",
                method: 'post',
                data: {
                    _token: CSRF_TOKEN,
                    by: "{{ Auth::user()->username }}",
                    pid: __pid,
                    opt: __opt
                },
                success: function(result) {
                    //Color the option box accordingly
                    if (result == "SUCCESS") {
                        right_nos = parseInt($("#right-" + __pid).text())
                        $("#right-" + __pid).text(" " + (right_nos + 1) + " ")

                        __el.removeClass("btn-outline-primary btn-rounded")
                        __el.addClass("btn-success")
                    } else if (result == "FAILURE") {
                        wrong_nos = parseInt($("#wrong-" + __pid).text())
                        $("#wrong-" + __pid).text(" " + (wrong_nos + 1) + " ")

                        __el.removeClass("btn-outline-primary btn-rounded")
                        __el.addClass("btn-danger")
                    }

                    __el.html(PRETEXT);
                }
            });
        });

        function remote(e) {
            $("#req-text").html("<i class='fas fa-2x fa-spinner fa-spin'></i>")
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ $feeditem_ajax_url }}",
                method: 'post',
                data: {
                    _token: CSRF_TOKEN,
                    idx: JSON.stringify(feed_idx),
                },
                success: function(result) {
                    //console.log("GOT: " + result)

                    /**
                     * Whatever we get has two parts: the IDX list and the current result.
                     * append the current IDX list to the feed_idx variable, and show 
                     * the current result.
                     */

                    feed_total = result;
                    feed_idx = feed_total['idx'];

                    result = feed_total['result'];

                    result.forEach(feeditem => {

                        /**
                         * tags are there is posts as well as Worksheets
                         */
                        tags_string = ""
                        tags_list = JSON.parse(feeditem.tags);
                        tags_list.forEach(tag => {
                            tags_string += ` <a style="text-decoration:none" class="badge badge-secondary-light" href="{{ config('app.url') }}/tags/` + tag + `">` + tag + `</a> `;
                        });

                        if (feeditem.itemT == "post") {
                            $("#feed_holder").append(`

<div class="feed_item">
    ${feeditem.pretext}
    ${postitem_body(feeditem)}
</div>

                        `);
                        } else {
                            

                            $("#feed_holder").append(`
<div class="feed_item">
    ${feeditem.pretext}
    ${wsitem_body(feeditem)}
</div>
                            `);
                        }
                    });
                    $("#req-text").html("Load More")
                }
            });
        }

        $("#req").click(function(e) {
            remote(e);
        })
    })
</script>