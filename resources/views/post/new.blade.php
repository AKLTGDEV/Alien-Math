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

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.16/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.16/dist/summernote-bs4.min.js"></script>

<script src="{{ asset('thirdparty/jtr/jquery.tagsinput-revisited.js') }}"></script>
<link rel="stylesheet" href="{{ asset('thirdparty/jtr/jquery.tagsinput-revisited.css') }}" />

<?php

use Illuminate\Support\Facades\Input; ?>

<script>
    $(document).ready(function() {
        $("#body").summernote();
        $("#explanation").summernote();
        inputNumber($('.input-number'));
        var opt_nos = 2;

        $("#save").click(function(e) {
            e.preventDefault();
            $("#submit_mode").val(1);
            $("#f").submit();
        });

        $("#save-and-continue").click(function(e) {
            e.preventDefault();
            $("#submit_mode").val(2);
            $("#f").submit();
        });

        var tags_src = JSON.parse('<?php echo json_encode($tags_suggested); ?>');
        $('#tags').tagsInput({
            autocomplete: {
                source: tags_src
            }
        });

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

<div class="container main mt-2">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card">
                <div class="card-header">
                    Post MCQ
                </div>
                <div class="card-body">
                    <form id="f" action="{{ route('newpostsubmit') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input id="opt_nos" name="opt_nos" type="text" style="display:none;" value="4" />
                        <input id="submit_mode" name="submit_mode" type="text" style="display:none;" value="1" />
                        <!-- MODE 1: Submit and Show the Q
                             MODE 2: Submit and Post Another
                        -->

                        @if ( count( $errors ) > 0 )
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                            @endforeach
                        </div>
                        @endif

                        <!--<input placeholder="Question Title" class="form-control form-input" type="text" value="{{ Input::old('title') }}" name="title" id="title">-->

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
                                    <div class="col-md-6 op">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <input name="correct" id="copt" type="radio" aria-label="Correct Option" value="1">
                                                </div>
                                            </div>
                                            <input name="option1" placeholder="Option 1" id="opt1" type="text" class="form-control" aria-label="Option 1" value="{{ Input::old('option1') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 op">
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
                        <div class="row mt-1">
                            <div class="col">
                                <div class="row">
                                    <div class="col-md-6 op">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <input name="correct" id="copt" type="radio" aria-label="Correct Option" value="3">
                                                </div>
                                            </div>
                                            <input name="option3" placeholder="Option 3" id="opt3" type="text" class="form-control" aria-label="Option 1" value="{{ Input::old('option3') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 op">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <input name="correct" id="copt" type="radio" aria-label="Correct Option" value="4">
                                                </div>
                                            </div>
                                            <input name="option4" placeholder="Option 4" id="opt4" type="text" class="form-control" aria-label="Option 4" value="{{ Input::old('option4') }}">
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

                        <div class="form-group pt-3">
                            <h4 class="text-muted">
                                Detailed Explanation:
                            </h4>

                            <textarea id="explanation" name="explanation" style="width: 100%">
                                <p>{{ Input::old('explanation') }}</p>
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

                        <div class="form-group">
                            <label for="invite_people">
                                Tags
                            </label>
                            <div class="col-12" id="tag_h">
                                <input class="form-control" type="text" value="{{ Input::old('question_tags') }}" name="question_tags" data-role="tagsinput" id="tags">
                            </div>
                        </div>

                        <button class="btn btn-primary" id="save">
                            Save
                        </button>
                        <button class="btn btn-primary" id="save-and-continue">
                            Save and Continue
                        </button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection