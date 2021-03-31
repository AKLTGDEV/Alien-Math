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
                        <h1 class="text-bold">{{$right}}/{{$total}}</h1>
                        <h4>Completed in {{$mins}} minutes</h4>

                        <a class="btn btn-sm btn-secondary mt-1 mb-2" href="{{ route('stats') }}">
                            Stats
                        </a>

                        <h5 class="text-secondary">Share Your Result:</h5>

                        <?php
                        $telegramURL = "https://telegram.me/share/url?url=" . urlencode(route('wsresult', [$shareid])) . "&text=Check+my+result+on+CrowDoubt!";
                        ?>
                        
                        <!--<a class="btn btn-outline-primary" href="https://www.facebook.com/sharer/sharer.php?u={{ route('wsresult', [$shareid]) }}&display=popup">FB</a>-->
                        <div class="ml-1 fb-share-button" data-href="{{ route('wsresult', [$shareid]) }}" data-layout="button_count" data-size="large">
                            <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fdevelopers.facebook.com%2Fdocs%2Fplugins%2F&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">
                                Share
                            </a>
                        </div>
                        <a href="https://api.whatsapp.com/send?text={{ route('wsresult', [$shareid]) }} Check+my+result+on+CrowDoubt!" class="btn btn-outline-success target=" _blank">WhatsApp</a>
                        <a href="{{ $telegramURL }}" class="btn btn-outline-info" target="_blank">Telegram</a>
                    </div>
                    @else
                    <div class="card-text text-center mt-1">
                        <h1 class="text-bold">
                            Error occured during the test.
                        </h1>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-12">
                            <div class="fb-comments" data-href="{{Request::url()}}" data-numposts="5"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection