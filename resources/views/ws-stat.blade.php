@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/newws.css') }}">

<div class="global-container">
    <div class="card login-form">
        <div class="card-body">
            <h5 class="card-title text-center">
                Stats for {{ "'$ws'" }} <br>
                ({{ $attempts }} attemptees so far)
            </h5>
            <div class="card-text text-center mt-1">
                <a href="{{ route('namedprofile', [$username]) }}">{{ "@".$username }}</a> got:
                <h1 class="text-bold">{{ $right }}/{{ $total }}</h1>
                <h4>Completed in {{ $mins }} minutes</h4>

                <a href="{{ route('wsanswer-1', [$ws_slug]) }}" class="btn btn-md btn-primary">Take the test</a>
            </div>
        </div>
    </div>
</div>

@endsection