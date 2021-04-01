<script>
    function saqitem_body(feeditem) {

        pid = feeditem.pid;

        return `
        
<div class="card feed_item">
    <div class="card-header d-flex justify-content-between align-items-center" style="display: inline-flex">
        <div class="dropdown no-arrow">
            <button class="btn btn-link btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false"
                type="button"><i class="fas fa-ellipsis-v text-gray-400"></i></button>
            <div class="dropdown-menu shadow dropdown-menu-right animated--fade-in" role="menu">
                <a href="#" id="XYZ" class="dropdown-item" role="presentation">
                    &nbsp;Report
                </a>
                <a href="{{ config('app.url') }}/saq/edit/${feeditem.id}" id="XYZ" class="dropdown-item" role="presentation">
                    &nbsp;Edit
                </a>
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
    </div>
</div>

        `;
    }
</script>