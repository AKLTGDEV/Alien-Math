<script>
    jQuery(document).ready(function($) {
        var q_types = JSON.parse('<?php echo json_encode($q_types); ?>');
        var q_ids = JSON.parse('<?php echo json_encode($q_ids); ?>');
        var q_ratings = JSON.parse('<?php echo json_encode($q_ratings); ?>');
        var q_colors = JSON.parse('<?php echo json_encode($q_colors); ?>');

        var q_ctx = document.getElementById('q_chart').getContext('2d');
        var q_chart = new Chart(q_ctx, {
            "type": "bar",
            "data": {
                "labels": q_types,
                "datasets": [{
                    "label": "Rating",
                    "data": q_ratings,
                    "backgroundColor": q_colors,
                }]
            },
            "options": {
                "maintainAspectRatio": false,
                "legend": {
                    "display": false
                },
                "title": {}
            }
        })
    })
</script>