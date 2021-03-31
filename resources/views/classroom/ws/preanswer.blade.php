@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/newws.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<script>
    $(document).ready(function() {
        $("#start").click(function(e) {
            window.location.href = "{{ route('class_ws_answer', [$cid, $wsname]) }}";
        })
    })
</script>

<div class="global-container">
    <div class="card login-form">
        <div class="card-body">
            <h3 class="card-title text-center">
                Worksheet #1 - {{ $ws['title'] }}
            </h3>
            <div class="card-text">
                <h6>By {{ $author->name }}</h6>

                <div class="row">
                    <ul>
                        <li>{{ $ws['nos'] }} Questions</li>
                        <li>To be completed in {{ $ws['time'] }} Minutes</li>
                    </ul>
                </div>
                <div class="row" style="margin-top:3%">
                    <div class="col-md-10 col-md-offset-1">
                        <button id="start" class="btn btn-info" style="float: right">
                            Start
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection