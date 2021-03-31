@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/newws.css') }}">

<script>
    jQuery(document).ready(function() {
        $("title").text(`Create Username / {{ config('app.name', 'Crowdoubt') }}`);
    })
</script>

<?php

use Illuminate\Support\Facades\Input;
?>

<div class="global-container">
    <div class="card login-form">
        <div class="card-body">
            <h3 class="card-title text-center">
                Create Username
            </h3>
            <div class="card-text text-center mt-1">
                @if ( count( $errors ) > 0 )
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
                <form action="{{ route('createusername_submit') }}" method="post">
                    <div class="form-horizontal">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-12">
                                <input placeholder="Enter a username" class="form-control form-input" type="text" value="{{ Input::old('username') }}" name="username" id="username">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-outline-primary btn-sm">Next</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection