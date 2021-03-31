<script>
    function wsitem_body(feeditem) {
        // WS
        if (!feeditem.attempted) {
            att_anchor = '<a href="{{ config('
            app.url ') }}/worksheets/answer/' + feeditem.slug + '" class="btn btn-sm btn-outline-primary waves-effect">Attempt</a>';
        } else {
            att_anchor = '<a href="{{ config('
            app.url ') }}/worksheets/answer/' + feeditem.slug + '" class="btn btn-sm btn-outline-info waves-effect">Attempted</a>';
        }
        attempt_string =
            `<div class="attempt mt-1">` + att_anchor + `</div>`;

        header_string = `
        <a href="#" id="XYZ" class="dropdown-item" role="presentation">
            &nbsp;Report
        </a>
        `;
        if(feeditem.own == true){
            header_string = `
            <a href="{{ config('app.url ') }}/worksheets/edit?wsname=${feeditem.encname}" id="XYZ" class="dropdown-item" role="presentation">
                &nbsp;Edit Worksheet
            </a>
            <a href="{{ config('app.url ') }}/worksheets/remove/${feeditem.id}" id="XYZ" class="dropdown-item text-danger" role="presentation">
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
        <span>
            <a style="text-decoration:none" class="avatar mx-auto white"
                href="{{ config('app.url') }}/u/` + feeditem.username + `">
                <img style="height: 40px;" src="` + feeditem.profilepic + `" alt="avatar mx-auto white"
                    class="rounded-circle img-fluid">
                ` + feeditem.name + `
            </a>
        </span>
        <p class="card-text">
            <ul>
                <li>` + feeditem.nos + ` Questions</li>
                <li>To be completed in ` + feeditem.mins + ` Minutes</li>
                <li>` + feeditem.attempts + ` Attemptees</li>
            </ul>
        </p>
        <div class="section-fluid">
            <div class="ws-tags-list">
                ` + tags_string + `
            </div>
        </div>
        ` + attempt_string + `
    </div>
</div>

                            `;
    }
</script>