<script>
    function wsitem_body(feeditem) {
        var isadmin = {{ $isadmin }};

        // WS
        tags_string = ""
        tags_list = JSON.parse(feeditem.tags);
        tags_list.forEach(tag => {
            tags_string += ` <a style="text-decoration:none" class="badge badge-secondary-light" href="{{ config('app.url') }}/tags/` + tag + `">` + tag + `</a> `;
        });

        if (feeditem.attempted == true) {
            var attempt_string = `<div class="attempt mt-1"><a href="{{ config('app.url ') }}/class/wspreanswer/{{ $class->id }}/${feeditem.encname}" class="btn btn-outline-info btn-sm shadow mt-1">Attempted</a></div>`;
        } else {
            var attempt_string = `<div class="attempt mt-1"><a href="{{ config('app.url ') }}/class/wspreanswer/{{ $class->id }}/${feeditem.encname}" class="btn btn-outline-primary btn-sm shadow mt-1">Attempt</a></div>`;
        }

        if (feeditem.attempted == true) {
            var prev_string = `<a href="{{ config('app.url ') }}/class/wspreview/{{ $class->id }}/${feeditem.encname}" class="btn btn-outline-info btn-sm shadow mt-1">Preview</a>`;
        } else {
            var prev_string = ``;
        }

        header_string = `
        <a href="#" id="XYZ" class="dropdown-item" role="presentation">
            &nbsp;Report
        </a>
        `;
        if(isadmin == true){
            header_string = `
            <a href="{{ config('app.url ') }}/class/wsedit/{{ $class->id }}?wsname=${feeditem.encname}" id="XYZ" class="dropdown-item" role="presentation">
                &nbsp;Edit Worksheet
            </a>
            <a href="{{ config('app.url ') }}/class/wsgetjson/{{ $class->id }}?wsname=${feeditem.encname}" id="XYZ" class="dropdown-item" role="presentation">
                &nbsp;Get JSON
            </a>
            <a href="{{ config('app.url ') }}/class/wsremove/{{ $class->id }}?wsname=${feeditem.encname}" id="XYZ" class="dropdown-item text-danger" role="presentation">
                &nbsp;Delete Worksheet
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
                    <a style="text-decoration:none" class="avatar mx-auto white" href="{{ config('app.url') }}/u/` + feeditem.username + `">
                        <img style="height: 40px;" src="` + feeditem.profilepic + `" alt="avatar mx-auto white" class="rounded-circle img-fluid">
                        ` + feeditem.name + `
                    </a>
                    <div class="text-secondary d-none d-sm-inline-block">
                        ${feeditem.samay}
                    </div>
            </span>
        </div>
        <p class="card-text">
            <ul>
                <li>` + feeditem.content.nos + ` Questions</li>
                <li>To be completed in ` + feeditem.content.time + ` Minutes</li>
            </ul>
        </p>
        <div class="section-fluid">
            <div class="ws-tags-list">
                ` + tags_string + `
            </div>
        </div>

        ${prev_string}
        ${attempt_string}

    </div>
</div>
                            `;
    }
</script>