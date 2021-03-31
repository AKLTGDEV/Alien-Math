<script>
    jQuery(document).ready(function($) {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var feed_idx = [];

        remote(null);

        function remote(e) {
            $("#req-text").html("<i class='fas fa-2x fa-spinner fa-spin'></i>")
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ $ajax_url }}",
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
                        if (feeditem.itemT == "post" || feeditem.itemT == "BANKpost") {
                            $("#posts_holder").append(`
                                <div class="feed_item col-sm-4">
                                    ${mcq(feeditem)}
                                </div>

                            `);
                        } else {
                            $("#posts_holder").append(`
                                <div class="feed_item col-sm-4">
                                    ${subjective(feeditem)}
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

        /**
         * Implement them Tests logic
         */
        @if(isset($qb_index))

        var q_list = [];

        $('#posts_holder').on('change', '.qselect', function(e) {
            var qtype = $(this).attr("qtype");
            var qsid = $(this).attr("qsid");

            if (this.checked) {
                q_list.push({
                    "TYPE": qtype,
                    "ID": qsid
                });
            } else {
                index = 0;
                q_list.forEach(q => {
                    if (q['TYPE'] == qtype && q['ID'] == qsid) {
                        q_list.splice(index, 1);
                    }
                    index++;
                });
            }
            $("#current-test-qnos").text(q_list.length);
        });

        $("#current-test-proceed").click(function(e) {
            $.ajax({
                url: "{{ route('qbank_tests_addq') }}",
                method: 'post',
                data: {
                    _token: CSRF_TOKEN,
                    qlist: JSON.stringify(q_list),
                },
                success: function(result) {
                    if(result.status == "ok"){
                        // Proceed to Tests
                        window.location.href = "{{ route('qbank_index_tests') }}";
                    } else {
                        alert(result.msg);
                    }
                }
            });
        })

        @endif
    })

    function mcq(feeditem) {
        opts_nos = feeditem.opt_nos;

        var option_classes = [];
        for (let k = 1; k <= opts_nos; k++) {
            current_class = null;

            if (feeditem.given == null || k != feeditem.given) {
                current_class = 'btn-outline-primary btn-rounded waves-effect';
            } else {
                if (feeditem.given == correct) {
                    current_class = 'btn-success';
                } else {
                    current_class = 'btn-danger';
                }
            }

            option_classes.push(current_class);
        }

        $options_string_set = "";
        for (let t = 1; t <= opts_nos; t++) {
            $options_string_set += `

<div class="row">
    <div class="col-12 mt-sm-1">
        <div style="width: 100%; overflow: auto;" class="option btn ${option_classes[t-1]}">
            ${ feeditem.options[t-1] }
        </div>
    </div>
</div>
            `;
        }

        var bottom_text = "";
        if (feeditem.itemT == "post") {
            bottom_text = `<span class="badge badge-success text-bold">Public</span>`;
        } else if (feeditem.itemT == "BANKpost") {
            bottom_text = `<span class="badge badge-warning text-bold">Private</span>`;
        }

        if (feeditem.itemT != "post") { // Must be from the bank
            bottom_text += `
            <a type="button" class="btn btn-sm btn-outline-primary" href="{{ config('app.url') }}/qb/topic/${feeditem.topic.id}">
                Topic <span class="ml-1 badge badge-light">${feeditem.topic.name}</span>
            </a>
            <a type="button" class="btn btn-sm btn-outline-primary" href="{{ config('app.url') }}/qb/subtopic/${feeditem.topic.id}">
                Sub-Topic <span class="ml-1 badge badge-light">${feeditem.subtopic.name}</span>
            </a>
            <input type="checkbox" class="qselect" qtype="PVT" qsid="${feeditem.pid}" id="qs-${feeditem.id}" aria-label="Select Question">
            `;
        } else {
            bottom_text += `
            <input type="checkbox" class="qselect" qtype="PUBLIC" qsid="${feeditem.pid}" id="qs-${feeditem.id}" aria-label="Select Question">
            `;
        }


        return `
        
<div class="card feed_item">
    <div class="card-body">
        <p class="card-text" id="fi-body">
            ` + feeditem.body + `
        </p>
        
        <div class="section-fluid mt-1" id="__opts__">
            ${$options_string_set}
        </div>
    </div>
    <div class="card-footer">
        ${bottom_text}
    </div>
</div>

        `;
    }




    function subjective(feeditem) {
        return `
        
<div class="card feed_item">
    <div class="card-body">
        <p class="card-text" id="fi-body">
            ` + feeditem.body + `
        </p>
    </div>
    <div class="card-footer">
        <span class="badge badge-warning text-bold">Private</span>
        <a type="button" class="btn btn-sm btn-outline-primary" href="{{ config('app.url') }}/qb/topic/${feeditem.topic.id}">
            Topic <span class="ml-1 badge badge-light">${feeditem.topic.name}</span>
        </a>
        <a type="button" class="btn btn-sm btn-outline-primary" href="{{ config('app.url') }}/qb/subtopic/${feeditem.topic.id}">
            Sub-Topic <span class="ml-1 badge badge-light">${feeditem.subtopic.name}</span>
        </a>
        <input type="checkbox" class="qselect" qtype="PVT" qsid="${feeditem.pid}" id="qs-${feeditem.id}" aria-label="Select Question">
    </div>
</div>

        `;
    }
</script>