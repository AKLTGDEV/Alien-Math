@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/newws.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="{{ asset('thirdparty/bootstrap-tagsinput.js') }}"></script>

<div class="global-container">
    <div class="card login-form">
        <div class="card-body">
            <h3 class="card-title text-center">
                "{{$class->name}}"
            </h3>
            <div class="card-text">
                {{ csrf_field() }}
                <h5 class="text-center">
                    This is an invite-only classroom.
                </h5>
            </div>
        </div>
    </div>
</div>

@endsection