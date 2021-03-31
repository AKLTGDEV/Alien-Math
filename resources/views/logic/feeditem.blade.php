<script>
    jQuery(document).ready(function() {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        jQuery('.option').click(function(e) {
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
                }
            });
        });
    });
</script>