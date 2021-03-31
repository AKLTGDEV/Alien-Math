<div class="card" id="upcomingWork">
    <div class="card-header">
        Pending Uploads <a class="action" href="#"><i class="fas fa-bars"></i></a>
    </div>
    <div class="card-body">
        <ul class="list-group">
            @if (count($docs) == 0)
            <li class="list-group-item">
                None
            </li>
            @else
            <?php foreach ($docs as $doc) { ?>
                <li class="list-group-item">
                    <div class="lead text-primary">{{ $doc['title'] }}</div>
                    <div class="text-info">To be completed in {{ $doc['time'] }} minutes</div>
                    <div class="text-info">Status: {{ $doc['accepted'] == 1 ? "Accepted & Posted" : "Pending" }}</div>
                </li>
            <?php } ?>
            @endif
        </ul>
    </div>
</div>