@extends('layouts.app')
@section('content')


<link href="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/css/suneditor.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/suneditor.min.js"></script>

<script src="{{ asset('thirdparty/jtr/jquery.tagsinput-revisited.js') }}"></script>
<link rel="stylesheet" href="{{ asset('thirdparty/jtr/jquery.tagsinput-revisited.css') }}" />

<script type="module">
    $(document).ready(function(e) {
        const body_editor = SUNEDITOR.create((document.getElementById('body') || 'body'), {
            minWidth: "100%",
        });
        body_editor.onChange = (contents, core) => {
            body_editor.save();
        }

        const exp_editor = SUNEDITOR.create((document.getElementById('explanation') || 'explanation'), {
            minWidth: "100%",
        });
        exp_editor.onChange = (contents, core) => {
            exp_editor.save();
        }

        $('#topics').tagsInput();
        var tags_src = JSON.parse('<?php echo json_encode($topics); ?>');

        $("#topics-holder").click(function(e) {
            $("#TopicsModal").modal('show');
        });

        $("#tags-done").click(function(e) {
            var tag_selections = [];

            for (let k = 0; k < tags_src.length; k++) {
                if ($("#tag-" + k).is(':checked')) {
                    tag_selections.push(tags_src[k]);
                }
            }

            tag_selections.forEach(T => {
                $("#topics").addTag(T)
            });

            $("#TopicsModal").modal('hide')

        })
    })
</script>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card">
                <div class="card-header">{{ __('Create SQA') }}</div>

                <div class="card-body">
                    @if ( count( $errors ) > 0 )
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                        @endforeach
                    </div>
                    @endif

                    <form action="{{ route('newsqasubmit') }}" method="post">
                        @csrf

                        <div class="container-fluid">
                            <div class="form-group">
                                <textarea name="body" id="body">Enter Question Body here</textarea>
                            </div>

                            <div class="form-group">

                                <h4 class="text-muted">
                                    Enter the options in the correct order
                                </h4>

                                <div class="input-group mb-1">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="opt1-label">Option 1</span>
                                    </div>
                                    <input name="O1" type="text" class="form-control" aria-label="Default" aria-describedby="opt1-label">
                                </div>

                                <div class="input-group mb-1">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="opt2-label">Option 2</span>
                                    </div>
                                    <input name="O2" type="text" class="form-control" aria-label="Default" aria-describedby="opt2-label">
                                </div>


                                <div class="input-group mb-1">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="opt3-label">Option 3</span>
                                    </div>
                                    <input name="O3" type="text" class="form-control" aria-label="Default" aria-describedby="opt3-label">
                                </div>


                                <div class="input-group mb-1">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="opt4-label">Option 4</span>
                                    </div>
                                    <input name="O4" type="text" class="form-control" aria-label="Default" aria-describedby="opt4-label">
                                </div>

                            </div>


                            <div class="form-group pt-3">
                                <h4 class="text-muted">
                                    Detailed Explanation:
                                </h4>

                                <textarea name="explanation" id="explanation">Enter the detailed explanation here</textarea>
                            </div>

                            <div class="form-group">
                                <label for="grade" class="text-muted">Select Grade</label>
                                <select class="form-control" id="grade" name="grade">
                                    <option value="P1">Primary 1</option>
                                    <option value="P2">Primary 2</option>
                                    <option value="P3">Primary 3</option>
                                    <option value="P4">Primary 4</option>
                                    <option value="P5">Primary 5</option>
                                    <option value="P6">Primary 6</option>

                                    <option value="S1">Secondary 1</option>
                                    <option value="S2">Secondary 2</option>
                                    <option value="S3">Secondary 3</option>
                                    <option value="S4">Secondary 4</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="difficulty" class="text-muted">Select difficulty</label>
                                <select class="form-control" id="difficulty" name="difficulty">
                                    <option value="1">Easy</option>
                                    <option value="2">Medium</option>
                                    <option value="3">Hard</option>
                                </select>
                            </div>

                            <div class="form-group" id="topics-holder">
                                <label for="topics" class="text-muted">Attach Topics</label>
                                <input class="form-control" type="text" name="topics" data-role="tagsinput" id="topics">
                            </div>

                        </div>

                        <button class="btn ntn-md btn-primary" type="submit">Submit</button>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>






<!-- TOPICS MODAL CODE BEGIN -->

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

<div class="modal shadow" id="TopicsModal">
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
                        foreach ($topics as $t) {
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

<!-- TOPICS MODAL CODE END -->


@endsection