<?php
$difficulty = null;

switch ($question->difficulty) {
    case 1:
        $difficulty = "Easy";
        break;

    case 2:
        $difficulty = "Moderate";
        break;
    case 3:
        $difficulty = "Advanced";
        break;

    default:
        # code...
        break;
}
?>

    <div class="container-fluid mt-2">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    @foreach($question->getTopics() as $tag)
                    <span class="badge badge-secondary">{{ $tag }}</span>
                    @endforeach
                </div>
            </div>

            <div class="col-md-6">
                <div class="row">Grade: {{ $question->type }}</div>
                <div class="row">Difficulty: {{ $difficulty }}</div>
                <div class="row">Uploaded by {{ "@".$question->uploader() }}</div>
            </div>
        </div>
    </div>