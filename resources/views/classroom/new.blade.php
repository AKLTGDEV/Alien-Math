@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/newws.css') }}">
<script src="{{ asset('thirdparty/jtr/jquery.tagsinput-revisited.js') }}"></script>
<link rel="stylesheet" href="{{ asset('thirdparty/jtr/jquery.tagsinput-revisited.css') }}" />
<link href="{{ config('app.url') }}/css/compws.css" rel="stylesheet">

<style>
    .name-field {
        background-color: #fff;
        border: 1px solid #ccc;
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        display: block;
        padding: 4px;
        color: #555;
        border-radius: 4px;
        max-width: 100%;
        line-height: 22px;
        cursor: text;
    }

    .name-field input {
        border: none;
        box-shadow: none;
        outline: none;
        background-color: transparent;
        padding: 0 6px;
        margin: 0;
        width: 100%;
        max-width: inherit;
    }
</style>

<?php

use Illuminate\Support\Facades\Input;
?>


<script type="text/javascript">
    jQuery(document).ready(function() {
        $('#tags').tagsInput();
        //$('#invites').tagsInput();

        var tags_src = JSON.parse('<?php echo json_encode($tags_suggested); ?>');
        console.log(tags_src)

        $("#tags_h").click(function(e) {
            $("#TagsModal").show();
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

            $("#TagsModal").hide()

        })
    })
</script>

<div class="global-container">
    <div class="card login-form">
        <div class="card-body">
            <h3 class="card-title text-center">
                Create a Classroom
                @if ( count( $errors ) > 0 )
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                    @endforeach
                </div>
                @endif
            </h3>
            <div class="card-text">
                <form id="cws_form" action="{{ config('app.url') }}/class/newsubmit" method="post">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <label for="invite_people">
                            Name of the Classroom
                        </label>
                        <div class="col-12 name-field">
                            <input class="form-control" type="text" value="{{ Input::old('name') }}" name="name" id="name">
                        </div>
                    </div>

                    <div class="form-group" style="display: none;">
                        <label for="invite_people">
                            Invite Students
                        </label>
                        <div class="col-12">
                            <input placeholder="Enter username" class="form-control" type="text" value="{{ Input::old('invites') }}" name="invites" data-role="tagsinput" id="invites">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="invite_people">
                            Tags
                        </label>
                        <div class="col-12" id="tags_h">
                            <input class="form-control" type="text" value="{{ Input::old('tags') }}" name="tags" data-role="tagsinput" id="tags">
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


@endsection