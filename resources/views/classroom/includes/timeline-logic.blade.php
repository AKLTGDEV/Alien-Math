@include('classroom.includes.postitem_body')
@include('classroom.includes.wsitem_body')
@include('classroom.includes.noteitem_body')

<script>
    $(document).ready(function() {
        /**
         * Prepare Q answer submission
         *
         */

        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var feed_idx = [];

        remote(null);

        //jQuery('.class_q_option').click(function(e) {
        $("#feed-holder").on('click', '.class_q_option', function(e) {
            var PRETEXT = $(this).text();
            $(this).html("<i class='fas fa-2x fa-spinner fa-spin'></i>")

            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });

            __el = $(this)
            qname = $(this).attr("unq_name")
            __opt = $(this).attr("opt")

            $.ajax({
                url: "{{ route('class_ansq', [$class->id]) }}",
                method: 'post',
                data: {
                    _token: CSRF_TOKEN,
                    qname: qname,
                    given: __opt
                },
                success: function(result) {
                    //Color the option box accordingly
                    if (result == "SUCCESS") {
                        __el.removeClass("btn-outline-primary btn-rounded")
                        __el.addClass("btn-success")
                    } else if (result == "FAILURE") {
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
                url: "{{ route('classroomtimeline_content', [$class->id]) }}",
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

                        if (feeditem.itemT == "post") {
                            //Question
                            $("#feed-holder").append(postitem_body(feeditem));
                        } else if (feeditem.itemT == "ws") {
                            //WS
                            $("#feed-holder").append(wsitem_body(feeditem));
                        } else {
                            //NOTE
                            $("#feed-holder").append(noteitem_body(feeditem));
                        }
                    });
                    $("#req-text").html("Load Posts")
                }
            });
        }

        $("#req").click(function(e) {
            remote(e);
        })
    })
</script>