@extends('layouts.app')

@section('content')


<script>
    $(document).ready(function() {

        $("#stats_btn").click(function(e) {
            $("#statsform").submit();
        })

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

<?php

use App\UserModel;
?>

<form id="statsform" action="{{ route('TSstats', [$TS->encname]) }}" method="get" style="display: none;">
    <input type="text" name="u" value="{{ Auth::user()->username }}">
</form>

<div class="container-fluid main">
    <div class="row">
        <div class="col-12">
            <div id="content" id="content">
                <div class="container-fluid">
                    <div class="d-sm-flex justify-content-between align-items-center mt-2 mb-2 py-1">
                        <h3 class="text-dark mb-0">
                            Test Series: "{{ $TS->name }}"
                            <button id="stats_btn" class="ml-4 btn btn-sm btn-outline-primary">
                                Stats
                            </button>

                            @if($author)
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('TSsettings', [$TS->encname]) }}">Settings</a>
                            <a href="#" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#postwsModal">
                                <i class="fas fa-plus">
                                </i>
                            </a>
                            @endif
                        </h3>
                    </div>


                    <div class="row">
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">

                                        <style>
                                            .item {
                                                min-width: 50%;
                                            }
                                        </style>

                                        @if(count($wslist) > 0)
                                        <?php foreach ($wslist as $ws) {
                                            //$tags = json_decode($ws['tags'], true);
                                            $tags = $ws['tags'];
                                            $ws['mine'] = false;
                                            ?>

                                            <div class="col-md item">
                                                @include('TS.wsitem')
                                            </div>

                                        <?php } ?>
                                        @else
                                        <div class="col-md item">
                                            No worksheets yet
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>




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

<div class="modal" tabindex="-1" role="dialog" id="postwsModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('TScomposews', [$TS->encname]) }}" method="get" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Post Worksheet</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>
                            Number of questions
                        </label>
                        <div class="row">
                            <div class="col-md-12">
                                <span class="input-number-decrement">–</span><input id="nos_Q" class="input-number" type="text" value="1" min="0" max="50" name="nos"><span class="input-number-increment">+</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Proceed</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection