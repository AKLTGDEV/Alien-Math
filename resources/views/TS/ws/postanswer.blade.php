@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/newws.css') }}">
<script>
    jQuery(document).ready(function() {
        $("title").text(`Test Results / {{ config('app.name', 'Crowdoubt') }}`);
        $("meta[property='og\\:title']").attr("content", `Test Results / {{ config('app.name', 'Crowdoubt') }}`);
    })
</script>


<div class="global-container">
    <div class="card login-form">
        <div class="card-body">
            @if(!$fucked)
            <div class="card-text text-center mt-1">
                <h1 class="text-bold">{{ $results['right'] }}/{{ $results['total'] }}</h1>
                <h4>Completed in {{$nettime}} minutes</h4>

                <a class="btn btn-sm btn-secondary mt-1 mb-2" href="{{ route('stats') }}">
                    Stats
                </a>
            </div>
            @else
            <div class="card-text text-center mt-1">
                <h1 class="text-bold">
                    Error occured during the test.
                </h1>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection