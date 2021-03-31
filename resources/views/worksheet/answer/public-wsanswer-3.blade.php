@extends('layouts.app')

@section('content')

<script>
    jQuery(document).ready(function() {
        $("title").text(`Test Results / {{ config('app.name', 'Crowdoubt') }}`);
        $("meta[property='og\\:title']").attr("content", `Test Results / {{ config('app.name', 'Crowdoubt') }}`);
    })
</script>


<div class="global-container">
    <div class="row mt-2">
        <div class="col-12 col-md-8 ml-md-auto mr-md-auto">
            <div class="card">
                <div class="card-body">

                    @if(!$fucked)
                    <div class="card-text text-center mt-1">
                        @if( env('CD_PWSA_RES') == true )
                        <h1 class="text-bold">{{$right}}/{{$total}}</h1>
                        <h4>Completed in {{$mins}} minutes</h4>

                        <a class="btn btn-sm btn-primary mt-1 mb-2" href="{{ route('register_public_attempt', [$slug, $public_id]) }}">
                            Login now to save your progress!
                        </a>
                        @else
                        <h2 class="text-bold text-success">
                            Congratulations on completing the test
                        </h2>
                        <a class="btn btn-sm btn-primary mt-1 mb-2" href="{{ route('register_public_attempt', [$slug, $public_id]) }}">
                            Login now to see your results!
                        </a>
                        @endif

                    </div>
                    @else
                    <div class="card-text text-center mt-1">
                        <h1 class="text-bold">
                            Error occured during the test.
                        </h1>
                    </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="lead text-bold">
                                Here are some other worksheets you could try:
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <style>
                            .item {
                                min-width: 50%;
                            }
                        </style>
                        <?php foreach ($others as $ws) {
                            $tags = json_decode($ws['tags'], true);
                            $ws['mine'] = false;
                            ?>

                            <div class="col-md item">
                                @include('includes.wsitem')
                            </div>

                        <?php } ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection