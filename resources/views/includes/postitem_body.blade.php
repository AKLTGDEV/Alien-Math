<script>
    function postitem_body(feeditem) {

        pid = feeditem.pid;

        given = feeditem.givenopt;
        correct = feeditem.correctopt;
        opts_nos = feeditem.opt_nos;


        var option_classes = [];
        for (let k = 1; k <= opts_nos; k++) {
            current_class = null;

            if (given == null || k != given) {
                current_class = 'btn-outline-primary btn-rounded waves-effect';
            } else {
                if (given == correct) {
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
        <div style="width: 100%; overflow: auto;" class="option btn ${option_classes[t-1]}" id="opt${t}_${pid}"
            pid="${pid}" opt="${t}">
            ${ feeditem.options[t-1] }
        </div>
    </div>
</div>
            `;
        }

        header_string = `
        <a href="#" id="XYZ" class="dropdown-item" role="presentation">
            &nbsp;Report
        </a>
        `;
        if(feeditem.own == true){
            header_string = `
            <a href="#" id="XYZ" class="dropdown-item text-danger" role="presentation">
                &nbsp;Delete Post
            </a>
            `
        }

        return `
        
<div class="card feed_item">
    <div class="card-header d-flex justify-content-between align-items-center" style="display: inline-flex">
        <div class="card-title">
            <span>
                <h4>` + feeditem.title + `</h4>
            </span>
        </div>
        <div class="dropdown no-arrow">
            <button class="btn btn-link btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false"
                type="button"><i class="fas fa-ellipsis-v text-gray-400"></i></button>
            <div class="dropdown-menu shadow dropdown-menu-right animated--fade-in" role="menu">
                ${header_string}
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="card-title">
            <span>
                <a style="text-decoration:none" class="avatar mx-auto white"
                    href="{{ config('app.url') }}/u/` + feeditem.username + `">
                    <img style="height: 40px;" src="` + feeditem.profilepic + `" alt="avatar mx-auto white"
                        class="rounded-circle img-fluid">
                    <span id="fi-name">
                        ` + feeditem.name + `
                    </span>
                </a>
            </span>
        </div>
        <p class="card-text" id="fi-body">
            ` + feeditem.body + `
        </p>
        <div class="section-fluid">
            <div class="post-tags-list">
                ` + tags_string + `
            </div>
        </div>
        <div class="section-fluid mt-1" id="__opts__">
            ${$options_string_set}
            <div class="answers mt-1">
                <span class="badge badge-success"><span id="right-` + pid + `"> ` + feeditem.right + `</span> </span>
                <span class="badge badge-danger"><span id="wrong-` + pid + `"> ` + feeditem.wrong + `</span> </span>
            </div>
        </div>
    </div>
</div>

        `;
    }
</script>