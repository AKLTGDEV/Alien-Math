@extends('layouts.app')
@section('content')


<link href="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/css/suneditor.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/suneditor.min.js"></script>

<script src="{{ asset('thirdparty/jtr/jquery.tagsinput-revisited.js') }}"></script>
<link rel="stylesheet" href="{{ asset('thirdparty/jtr/jquery.tagsinput-revisited.css') }}" />


<script>
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

        $("#grade").val("{{ $question->type }}");
        $("#difficulty").val("{{ $question->difficulty }}");

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
                <div class="card-header">{{ __("Editing SAQ #" . $question->id) }}</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    <form action="{{ route('editsaq.submit', [$question->id]) }}" method="post">
                        @csrf

                        <div class="container-fluid">
                            <div class="form-group">
                                <textarea name="body" id="body">
                                {{ $question->getbody() }}
                                </textarea>
                            </div>

                            <div class="form-group">
                                <label for="correct">Correct Answer</label>
                                <input value="{{ $question->correct }}" type="text" class="form-control" id="correct" name="correct">
                            </div>


                            <div class="form-group pt-3">
                                <h4 class="text-muted">
                                    Detailed Explanation:
                                </h4>

                                <textarea name="explanation" id="explanation">
                                {{ $question->getexplanation() }}
                                </textarea>
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
                                <input value="{{ $question->topics }}" class="form-control" type="text" name="topics" data-role="tagsinput" id="topics">
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