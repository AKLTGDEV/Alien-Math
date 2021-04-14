@extends('layouts.app')

@section('content')

<style>
    .option {
        height: 50px;
        margin: 1%;

    }

    .option-text {
        width: 100%;
        height: 100%;
        text-align: center;
    }

    .opts-holder {
        margin-left: 2%;
        margin-right: 2%;
    }

    .opt-selected {
        background: linear-gradient(90deg, rgba(254, 188, 188, 1) 0%, rgba(250, 228, 167, 1) 100%);
    }

    .holder-col {
        margin-top: 5px;
    }
</style>

<div class="container">
    <div class="row">
        <div class="col-12 holder-col">
            <div class="card card-default px-2 py-1">
                <div class="card-header text-muted">
                    <h4 class="mt-2">
                        Q{{ $question->id }}. <?php echo $question->getBody() ?>
                    </h4>
                </div>

                <div class="card-body container-fluid" id="content-body">
                    <h3 class="text-muted">
                        Correct Order:
                    </h3>

                    <div class="row text-success justify-content-center">
                        <div class="col-md-8">
                            <ul class="list-group">
                                <li class="list-group-item">{{ $question->O1 }}</li>
                                <li class="list-group-item">{{ $question->O2 }}</li>
                                <li class="list-group-item">{{ $question->O3 }}</li>
                                <li class="list-group-item">{{ $question->O4 }}</li>
                            </ul>
                        </div>
                    </div>

                </div>

                <div class="card-footer">
                    <div class="row">
                        <h4 class="mt-2 text-primary">
                            Detailed Explanation:
                        </h4>
                    </div>

                    <div class="row">
                        <?php echo $question->getExplanation() ?>
                    </div>
                </div>
            </div>

            @include('includes.qdetails')
            @include('includes.qvideos')

            <div class="row justify-content-center mt-4">
                <div class="col-md-8">
                    <a href="{{ route('video.q-attach', [$question->Table(), $question->id]) }}" class="btn btn-outline-primary" style="width: 100%">
                        Attach Videos to this Question
                    </a>
                </div>
            </div>

            <div class="row justify-content-center mt-4">
                <div class="col-md-8">
                    <a href="{{ route('editsqa', [$question->id]) }}" class="btn btn-outline-primary" style="width: 100%">
                        Edit This Question
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection