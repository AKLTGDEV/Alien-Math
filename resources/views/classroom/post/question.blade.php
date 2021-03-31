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

<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.16/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.16/dist/summernote-bs4.min.js"></script>
<link href="{{ config('app.url') }}/css/compws.css" rel="stylesheet">
<script src="{{ asset('thirdparty/jtr/jquery.tagsinput-revisited.js') }}"></script>
<link rel="stylesheet" href="{{ asset('thirdparty/jtr/jquery.tagsinput-revisited.css') }}" />

<?php

use Illuminate\Support\Facades\Input; ?>

<script>
    $(document).ready(function() {
        $("#body").summernote();
        inputNumber($('.input-number'));

        var opt_nos = 2;

        var tags_src = JSON.parse('<?php echo json_encode($tags_suggested); ?>');
        $('#tags').tagsInput();

        $("#tags_h").click(function(e) {
            $("#TagsModal").modal('show');
        });

        $("#tags-done").click(function(e) {
            //console.log("Done")
            var tag_selections = [];

            for (let k = 0; k < tags_src.length; k++) {
                //console.log($("#tag-"+k).is(':checked'));
                if ($("#tag-" + k).is(':checked')) {
                    tag_selections.push(tags_src[k]);
                }
            }

            tag_selections.forEach(T => {
                $("#tags").addTag(T)
            });

            $("#TagsModal").modal('hide')

        })

        $("#add-opt-btn").click(function(e) {
            opt_nos++;

            $("#new-opt-space").append(`

<div class="col-md-6 mt-1 mb-1">
    <div class="input-group">
        <div class="input-group-prepend">
            <div class="input-group-text">
                <input name="correct" id="copt" type="radio" aria-label="Correct Option" value="${opt_nos}">
            </div>
        </div>
        <input name="option${opt_nos}" placeholder="Option ${opt_nos}" id="opt${opt_nos}" type="text" class="form-control" aria-label="Option ${opt_nos}">
    </div>
</div>

            `);

            $("#opt_nos").attr("value", opt_nos);
        })

        $("#savebtn").click(function(e) {
            console.log("Save clicked");
        });
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

<div class="container-fluid main">
    <div class="row">
        <div class="col-12">
            <div id="content" id="content">
                <div class="container-fluid">
                    <div class="d-sm-flex justify-content-between align-items-center mb-4 py-2">
                        <h3 class="text-dark mb-1">Post Question on Classroom</h3>
                        <div class="btn btn-primary btn-sm d-none d-sm-inline-block" role="button" id="savebtn"><i class="fas fa-upload fa-sm text-white-50"></i>&nbsp;Save</div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-12">
                                        <form id="f" action="{{ route('CLR_postq_submit', [$class->id]) }}" method="post" enctype="multipart/form-data">
                                            {{ csrf_field() }}
                                            <input id="opt_nos" name="opt_nos" type="text" style="display:none;" value="2" />

                                            @if ( count( $errors ) > 0 )
                                            <div class="alert alert-danger">
                                                @foreach ($errors->all() as $error)
                                                {{ $error }}<br>
                                                @endforeach
                                            </div>
                                            @endif

                                            <div class="card card-default">
                                                <div class="card-title">
                                                    <input placeholder="Question Title" class="form-control form-input" type="text" value="{{ Input::old('title') }}" name="title" id="title">
                                                </div>
                                                <div class="card-body container-fluid">
                                                    <div class="row">
                                                        <div class="col-12 editable-holder">
                                                            <textarea id="body" name="Qbody" style="width: 100%">
                                                                    <p>{{ Input::old('Qbody') }}</p>
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
                                                                                <input name="correct" id="copt" type="radio" aria-label="Correct Option" value="1">
                                                                            </div>
                                                                        </div>
                                                                        <input name="option1" placeholder="Option 1" id="opt1" type="text" class="form-control" aria-label="Option 1" value="{{ Input::old('option1') }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-6 op">
                                                                    <div class="input-group">
                                                                        <div class="input-group-prepend">
                                                                            <div class="input-group-text">
                                                                                <input name="correct" id="copt" type="radio" aria-label="Correct Option" value="2">
                                                                            </div>
                                                                        </div>
                                                                        <input name="option2" placeholder="Option 2" id="opt2" type="text" class="form-control" aria-label="Option 2" value="{{ Input::old('option2') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row mt-2">
                                                        <div class="col-12">
                                                            <span class="text-secondary">
                                                                Add Extra Options
                                                                <div class="btn btn-sm btn-outline-primary" id="add-opt-btn">
                                                                    <span class="fa fa-plus">
                                                                    </span>
                                                                </div>
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- EXTRA OPTIONS SPACE -->

                                                    <div class="row" id="new-opt-space">
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="card card-default">
                                                <div class="card-title">
                                                    <h5>
                                                        Settings
                                                    </h5>
                                                </div>
                                                <div class="card-body container-fluid">
                                                    <div class="row">
                                                        <div class="col-12" id="tags_h">
                                                            <div class="form-group">
                                                                <label for="#tags">
                                                                    Tags
                                                                </label>
                                                                <div class="col-12 bootstrap-tagsinput">
                                                                    <input class="form-control" type="text" value="{{ Input::old('question_tags') }}" name="question_tags" data-role="tagsinput" id="tags">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col">
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

        <div class="modal shadow" id="TagsModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">

                        <p class="text-center text-info">
                            Select Tags
                        </p>

                        <div class="container-fluid searchable-container">
                            <div class="row">
                                <?php
                                $i = 0;
                                foreach ($tags_suggested as $t) {
                                    ?>
                                    <div class="col items">
                                        <div class="info-block block-info clearfix">
                                            <div class="square-box pull-left">
                                                <span class="glyphicon glyphicon-tags glyphicon-lg"></span>
                                            </div>
                                            <div data-toggle="buttons" class="btn-group bizmoduleselect">
                                                <label class="btn btn-default">
                                                    <div class="bizcontent">
                                                        <input type="checkbox" id="tag-{{ $i }}" name="" autocomplete="off">
                                                        <span class="glyphicon glyphicon-ok glyphicon-lg"></span>
                                                        <h5>
                                                            {{ $t }}
                                                        </h5>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                <?php $i++;
                                } ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="tags-done" type="button" class="btn btn-primary">Finish</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>



@endsection