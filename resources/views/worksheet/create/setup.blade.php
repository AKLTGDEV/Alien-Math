@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/newws.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<script src="{{ asset('thirdparty/jtr/jquery.tagsinput-revisited.js') }}"></script>
<link rel="stylesheet" href="{{ asset('thirdparty/jtr/jquery.tagsinput-revisited.css') }}" />

<script>
    $(document).ready(function($) {
        $('#invites').tagsInput();

        inputNumber($('.input-number'));

        $("#sub_create").click(function(e) {
            e.preventDefault();
            nos = $("#nos_Q").val();
            $("#cws_form").attr("action", "{{ route('composeworksheet', [0]) }}" + nos);
            $("#cws_form").submit();
        })
    });

    function inputNumber(el) {
        var min = el.attr('min') || false;
        var max = el.attr('max') || false;

        var els = {};

        els.dec = el.prev();
        els.inc = el.next();

        el.each(function() {
            init($(this));
        });

        function init(el) {

            els.dec.on('click', decrement);
            els.inc.on('click', increment);

            function decrement() {
                var value = el[0].value;
                value--;
                if (!min || value >= min) {
                    el[0].value = value;
                }
            }

            function increment() {
                var value = el[0].value;
                value++;
                if (!max || value <= max) {
                    el[0].value = value++;
                }
            }
        }
    }
</script>

<?php

use Illuminate\Support\Facades\Input;
?>

<div class="global-container">
    <div class="card login-form">
        <div class="card-body">
            <h3 class="card-title text-center">
                Create a Worksheet
                @if ( count( $errors ) > 0 )
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                    @endforeach
                </div>
                @endif
            </h3>
            <div class="card-text">
                <form id="cws_form" action="{{ config('app.url') }}/worksheets/new/" method="get">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <label for="invite_people">
                            Invite People
                        </label>
                        <div class="col-12">
                            <input class="form-control" type="text" value="{{ Input::old('invites') }}" name="invites" data-role="tagsinput" id="invites">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            Number of questions
                        </label>
                        <div class="row">
                            <div class="col-md-12">
                                <span class="input-number-decrement">–</span><input id="nos_Q" class="input-number" type="text" value="1" min="0" max="50"><span class="input-number-increment">+</span>
                            </div>
                        </div>
                    </div>

                    <button id="sub_create" class="btn btn-primary btn-block">
                        Proceed
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection