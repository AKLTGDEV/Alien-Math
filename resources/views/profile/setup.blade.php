@extends('layouts.app')

@section('content')

<style>
    html,
    body {
        height: 100%;
    }

    .global-container {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    form {
        padding-top: 10px;
        font-size: 14px;
        margin-top: 30px;
    }

    .card-title {
        font-weight: 300;
    }

    .btn {
        font-size: 14px;
    }


    .welcome-sect {
        margin-top: 20px;
    }

    .alert {
        font-size: 13px;
        margin-top: 20px;
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

<script type="text/javascript">
    jQuery(document).ready(function() {

        var tags_src = JSON.parse('<?php echo json_encode($tags_suggested); ?>');
        //console.log(tags_src)

        $("#done").click(function(e) {
            var tag_selections = [];

            for (let k = 0; k < tags_src.length; k++) {
                if ($("#tag-" + k).is(':checked')) {
                    tag_selections.push(tags_src[k]);
                }
            }

            inp = tag_selections.join();
            $("#T").val(inp);

            $("#f").submit();
        })
    })
</script>

<form id="f" action="{{ route('usereditsubmit') }}" method="post" enctype="multipart/form-data" style="display: none;">
    {{ csrf_field() }}
    <input type="text" name="tags" value="" id="T">
    <input type="text" name="bio" value="">
</form>

<div class="global-container container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card welcome-sect">
                <div class="card-body">
                    <h3 class="card-title text-center">Welcome to {{ config('app.name', 'Crowdoubt') }} !</h3>

                    <div class="card-text text-center text-info">
                        Hello, {{ $ext['fname'] }} &#128517; <br>
                        CrowDoubt is a place for teachers and students to collaborate, have classes, & create and attempt question papers. Online. For free.
                    </div>
                    
                    <div class="card-text text-center text-primary">
                        Select at least 2 tags from the list to begin
                    </div>

                    @if ( count( $errors ) > 0 )
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                        @endforeach
                    </div>
                    @endif

                    <div class="row mt-1 searchable-container">

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

                    <div class="card-footer">
                        <div class="btn btn-primary" id="done">
                            Done
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


@endsection