@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/newws.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="{{ asset('thirdparty/bootstrap-tagsinput.js') }}"></script>
<style>
    .input-number {
        width: 80px;
        padding: 0 12px;
        vertical-align: top;
        text-align: center;
        outline: none;
    }

    .input-number,
    .input-number-decrement,
    .input-number-increment {
        border: 1px solid #ccc;
        height: 40px;
        user-select: none;
    }

    .input-number-decrement,
    .input-number-increment {
        display: inline-block;
        width: 30px;
        line-height: 38px;
        background: #f1f1f1;
        color: #444;
        text-align: center;
        font-weight: bold;
        cursor: pointer;
    }

    .input-number-decrement:active,
    .input-number-increment:active {
        background: #ddd;
    }

    .input-number-decrement {
        border-right: none;
        border-radius: 4px 0 0 4px;
    }

    .input-number-increment {
        border-left: none;
        border-radius: 0 4px 4px 0;
    }
</style>

<script>
    $(document).ready(function() {

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

        inputNumber($('.input-number'));
    })
</script>

<div class="global-container">
    <div class="card login-form">
        <div class="card-body">
            <h3 class="card-title text-center">
                WS: "{{$doc->title}}"
            </h3>
            <div class="card-text">
                <form id="f" action="{{ route('admindocupload', [$doc['id']]) }}" method="get">
                    {{ csrf_field() }}

                    <h5 class="text-center text-primary">
                        Status: {{ $doc['accepted'] == 1 ? "Accepted & Posted" : "Pending" }} <br>
                        File: <a href="{{ route('admindocgetfile', [$doc['id']]) }}">{{ $doc['original_name'] }}</a>
                    </h5>

                    @if ($doc['notes'] != null)
                    <h5 class="text-center lead">
                        NOTES: {{ $doc['notes'] }}
                    </h5>
                    @endif

                    @if (!$doc['accepted'])
                    <span class="text-info">Number of questions </span>
                    <span class="input-number-decrement">–</span>
                    <input id="nos_Q" class="input-number" type="text" value="1" min="0" max="50" name="nos">
                    <span class="input-number-increment">+</span>
                    <button type="submit" id="proceed" class="btn btn-primary btn-block">
                        Proceed
                    </button>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

@endsection