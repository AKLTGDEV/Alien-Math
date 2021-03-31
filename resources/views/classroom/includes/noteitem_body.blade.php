<script>
    function noteitem_body(feeditem) {
        return `
<div class="card feed_item">
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
            ${feeditem.content.body}
        </p>
    </div>
</div>
                            `;
    }
</script>