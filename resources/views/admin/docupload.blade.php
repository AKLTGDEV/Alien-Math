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
        $('#tags').tagsInput();


        var tags_src = JSON.parse('<?php echo json_encode($tags_suggested); ?>');
        console.log(tags_src)

        $("#tags_H").click(function(e) {
            $("#TagsModal").modal('show');
        });

        $("#tags-done").click(function(e) {
            console.log("Done")
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
    })
</script>

<div class="container-fluid main">
    <div class="row">
        <div class="col-12">
            <div id="content" id="content">
                <div class="container-fluid">
                    <div class="d-sm-flex justify-content-between align-items-center mb-4">
                        <h3 class="text-dark mb-1 mt-2">Post Worksheet on Classroom</h3><a class="btn btn-primary btn-sm d-none d-sm-inline-block" role="button" href="#"><i class="fas fa-download fa-sm text-white-50"></i>&nbsp;Generate Report</a>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-12">
                                        <form id="f" action="{{ route('docupload_submit', [$doc->id]) }}" method="post" enctype="multipart/form-data">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="nos" value="{{ $nos }}" />

                                            @if ( count( $errors ) > 0 )
                                            <div class="alert alert-danger">
                                                @foreach ($errors->all() as $error)
                                                {{ $error }}<br>
                                                @endforeach
                                            </div>
                                            @endif

                                            <div class="card card-default">
                                                <div class="card-body container-fluid">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <input placeholder="Worksheet Title" class="form-control form-input" type="text" value="{{ $title }}" name="title" id="title">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
                                                                    <p>{{ Input::old('Qbody-'.$i) }}</p>
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
                                                                                    <input name="correct-{{$i}}" id="copt" type="radio" aria-label="Correct Option" value="1">
                                                                                </div>
                                                                            </div>
                                                                            <input name="option1-{{$i}}" placeholder="Option 1" id="opt1" type="text" class="form-control" aria-label="Option 1" value="{{ Input::old('option1-'.$i) }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-6 op">
                                                                        <div class="input-group">
                                                                            <div class="input-group-prepend">
                                                                                <div class="input-group-text">
                                                                                    <input name="correct-{{$i}}" id="copt" type="radio" aria-label="Correct Option" value="2">
                                                                                </div>
                                                                            </div>
                                                                            <input name="option2-{{$i}}" placeholder="Option 2" id="opt2" type="text" class="form-control" aria-label="Option 2" value="{{ Input::old('option2-'.$i) }}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-6 op">
                                                                        <div class="input-group">
                                                                            <div class="input-group-prepend">
                                                                                <div class="input-group-text">
                                                                                    <input name="correct-{{$i}}" id="copt" type="radio" aria-label="Correct Option" value="3">
                                                                                </div>
                                                                            </div>
                                                                            <input name="option3-{{$i}}" placeholder="Option 3" id="opt3" type="text" class="form-control" aria-label="Option 3" value="{{ Input::old('option3-'.$i) }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-6 op">
                                                                        <div class="input-group">
                                                                            <div class="input-group-prepend">
                                                                                <div class="input-group-text">
                                                                                    <input name="correct-{{$i}}" id="copt" type="radio" aria-label="Correct Option" value="4">
                                                                                </div>
                                                                            </div>
                                                                            <input name="option4-{{$i}}" placeholder="Option 4" id="opt4" type="text" class="form-control" aria-label="Option 4" value="{{ Input::old('option4-'.$i) }}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
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
                                                                <label for="invite_people">
                                                                    Tags
                                                                </label>
                                                                <div class="col-12 bootstrap-tagsinput" id="tags_H">
                                                                    <input class="form-control" type="text" value="{{ Input::old('tags') }}" name="tags" data-role="tagsinput" id="tags">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>
                                                                    Time (in minutes)
                                                                </label>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <span class="input-number-decrement">–</span><input id="nos_Q" class="input-number" type="text" value="{{ $mins }}" min="0" max="50" name="time"><span class="input-number-increment">+</span>
                                                                    </div>
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
                    <div class="lead text-center text-secondary mb-2">
                        Select Tags
                    </div>

                    <div class="container-fluid searchable-container">
                        <?php
                        $i = 0;
                        foreach ($tags_suggested as $t) {
                            ?>
                            <div class="row">

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

                            </div>
                        <?php $i++;
                        } ?>
                    </div>

                    <div class="modal-footer">
                        <button id="tags-done" type="button" class="btn btn-primary">Finish</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endsection