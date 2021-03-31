@extends('layouts.app')

@section('content')

<style>
    #content {
        background: #fff;
        margin-top: 1%;
        margin-bottom: 1%;
    }

    .note-editable {
        height: 150px;
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="{{ asset('thirdparty/bootstrap-tagsinput.js') }}"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.16/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.16/dist/summernote-bs4.min.js"></script>
<link href="{{ config('app.url') }}/css/compws.css" rel="stylesheet">

<script src="{{ asset('thirdparty/jtr/jquery.tagsinput-revisited.js') }}"></script>
<link rel="stylesheet" href="{{ asset('thirdparty/jtr/jquery.tagsinput-revisited.css') }}" />


<?php

use Illuminate\Support\Facades\Input; ?>

<script>
    $(document).ready(function() {
        <?php for ($i = 1; $i <= $nos; $i++) { ?>
            $("#body-{{$i}}").summernote();
        <?php } ?>
        inputNumber($('.input-number'));

        // Set up the collections
        var coll_list = JSON.parse('<?php echo json_encode($coll_list); ?>');

        <?php
        foreach ($coll_list as $coll => $qlist) {
            for ($i = 1; $i <= count($qlist); $i++) {
                $current_q = $qlist[$i - 1]; ?>

                $('#coll---{{ $current_q }}---{{ $coll }}').prop('checked', true);
                $('#coll_lab---{{ $current_q }}---{{ $coll }}').addClass("active");

        <?php }
        } ?>

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
</script>


<style>
    /* Important part */
    .modal-dialog {
        overflow-y: initial !important
    }

    .modal-body {
        height: 250px;
        overflow-y: auto;
    }

    .searchable-container label.btn-default.active {
        background-color: #007ba7;
        color: #FFF
    }

    .searchable-container label.btn-default {
        width: 100%;
        border: 1px solid #efefef;


    }

    .searchable-container label .bizcontent {
        width: 100%;
    }

    .searchable-container .btn-group {
        width: 100%;
    }

    .searchable-container .btn span.glyphicon {
        opacity: 0;
    }

    .searchable-container .btn.active span.glyphicon {
        opacity: 1;
    }
</style>

<div class="container-fluid main">
    <div class="row">
        <div class="col-12">
            <div id="content" id="content">
                <div class="container-fluid">
                    <div class="d-sm-flex justify-content-between align-items-center mb-4">
                        <h3 class="text-dark mb-1 mt-2">Edit Worksheet "{{ $title }}"</h3>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-12">
                                        <form id="f" action="{{ route('class_ws_edit_submit', [$class->id, $wsname]) }}" method="post" enctype="multipart/form-data">
                                            {{ csrf_field() }}
                                            <div class="alert alert-info">
                                                Note that only the question bodies and options could be edited.
                                            </div>

                                            @if ( count( $errors ) > 0 )
                                            <div class="alert alert-danger">
                                                @foreach ($errors->all() as $error)
                                                {{ $error }}<br>
                                                @endforeach
                                            </div>
                                            @endif

                                            <?php for ($i = 1; $i <= $nos; $i++) { ?>
                                                <div class="card card-default">
                                                    <div class="card-title">
                                                        <h5>
                                                            Question {{$i}}
                                                        </h5>
                                                    </div>
                                                    <div class="card-body container-fluid">
                                                        <div class="row">
                                                            <div class="col-12 editable-holder">
                                                                <textarea id="body-{{$i}}" name="Qbody-{{$i}}" style="width: 100%">
                                                                    <p>{{ $bodies[$i-1] }}</p>
                                                                </textarea>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col">
                                                                <div class="row">
                                                                    <div class="col-6 op">
                                                                        <div class="input-group">
                                                                            <div class="input-group-prepend">
                                                                                <div class="input-group-text">
                                                                                    <input name="correct-{{$i}}" id="copt" type="radio" aria-label="Correct Option" value="1" <?php if ($correct[$i - 1] == 1) echo "checked"; ?>>
                                                                                </div>
                                                                            </div>
                                                                            <input name="option1-{{$i}}" placeholder="Option 1" id="opt1" type="text" class="form-control" aria-label="Option 1" value="{{ $options['option1-'.$i] }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-6 op">
                                                                        <div class="input-group">
                                                                            <div class="input-group-prepend">
                                                                                <div class="input-group-text">
                                                                                    <input name="correct-{{$i}}" id="copt" type="radio" aria-label="Correct Option" value="2" <?php if ($correct[$i - 1] == 2) echo "checked"; ?>>
                                                                                </div>
                                                                            </div>
                                                                            <input name="option2-{{$i}}" placeholder="Option 2" id="opt2" type="text" class="form-control" aria-label="Option 2" value="{{ $options['option2-'.$i]  }}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-6 op">
                                                                        <div class="input-group">
                                                                            <div class="input-group-prepend">
                                                                                <div class="input-group-text">
                                                                                    <input name="correct-{{$i}}" id="copt" type="radio" aria-label="Correct Option" value="3" <?php if ($correct[$i - 1] == 3) echo "checked"; ?>>
                                                                                </div>
                                                                            </div>
                                                                            <input name="option3-{{$i}}" placeholder="Option 3" id="opt3" type="text" class="form-control" aria-label="Option 3" value="{{ $options['option3-'.$i]  }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-6 op">
                                                                        <div class="input-group">
                                                                            <div class="input-group-prepend">
                                                                                <div class="input-group-text">
                                                                                    <input name="correct-{{$i}}" id="copt" type="radio" aria-label="Correct Option" value="4" <?php if ($correct[$i - 1] == 4) echo "checked"; ?>>
                                                                                </div>
                                                                            </div>
                                                                            <input name="option4-{{$i}}" placeholder="Option 4" id="opt4" type="text" class="form-control" aria-label="Option 4" value="{{ $options['option4-'.$i]  }}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @if (count($collections) > 0)
                                                        <div class="mt-2 container-fluid searchable-container">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <h5>
                                                                        Collections
                                                                    </h5>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                @foreach($collections as $c)
                                                                <div class="col items">
                                                                    <div class="info-block block-info clearfix">
                                                                        <div class="square-box pull-left">
                                                                            <span class="glyphicon glyphicon-tags glyphicon-lg"></span>
                                                                        </div>
                                                                        <div data-toggle="buttons" class="btn-group bizmoduleselect">
                                                                            <label id="coll_lab---{{$i}}---{{ $c->encname }}" class="btn btn-default">
                                                                                <div class="bizcontent">
                                                                                    <input type="checkbox" id="coll---{{$i}}---{{ $c->encname }}" name="coll---{{$i}}---{{ $c->encname }}" autocomplete="off">
                                                                                    <span class="glyphicon glyphicon-ok glyphicon-lg"></span>
                                                                                    <h5>
                                                                                        {{ $c->name }}
                                                                                    </h5>
                                                                                </div>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <div class="card card-default">
                                                <div class="card-title">
                                                    <h5>
                                                        Settings
                                                    </h5>
                                                </div>
                                                <div class="card-body container-fluid">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>
                                                                    Time (in minutes)
                                                                </label>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <span class="input-number-decrement">–</span><input id="nos_Q" class="input-number" type="text" value="{{ $time }}" min="0" max="50" name="time"><span class="input-number-increment">+</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col">
                                                            <button class="btn btn-danger">
                                                                Cancel
                                                            </button>
                                                            <button type="submit" class="btn btn-primary">
                                                                Submit
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
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

@endsection