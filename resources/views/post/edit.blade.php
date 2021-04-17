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

        $("#grade").val("{{ $question->type }}");
        $("#difficulty").val("{{ $question->difficulty }}");

        var tags_src = JSON.parse('<?php echo json_encode($tags_suggested); ?>');
        $('#tags').tagsInput({
            autocomplete: {
                source: tags_src
            }
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


<div class="container main mt-2">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card">
                <div class="card-header">
                    Edit MCQ #{{ $question->id }}
                </div>
                <div class="card-body">
                    <form id="f" action="{{ route('editpost.submit', [$question->id]) }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input id="opt_nos" name="opt_nos" type="text" style="display:none;" value="2" />

                        @if ( count( $errors ) > 0 )
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                            @endforeach
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-12 editable-holder">
                                <textarea id="body" name="Qbody" style="width: 100%">
                                    <p>{{ $question->getBody() }}</p>
                                </textarea>
                            </div>

                        </div>


                        <?php $opt_count = 1; ?>
                        @foreach($opts as $opt)
                        <div class="input-group mb-1">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <input name="correct" id="copt" type="radio" aria-label="Correct Option" value="{{ $opt_count }}" <?php if ($question->correctopt == $opt_count) {
                                                                                                                                            echo " checked";
                                                                                                                                        } ?>>
                                </div>
                            </div>
                            <input name="option{{ $opt_count }}" placeholder="Option {{ $opt_count }}" id="opt{{ $opt_count }}" type="text" class="form-control" aria-label="Option {{ $opt_count }}" value="{{ $opt }}">
                        </div>

                        <?php $opt_count++; ?>
                        @endforeach

                        <div class="form-group pt-3">
                            <h4 class="text-muted">
                                Detailed Explanation:
                            </h4>

                            <textarea id="explanation" name="explanation" style="width: 100%">
                                <p>{{ $question->getExplanation() }}</p>
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

                        <input type="text" style="display: none;" name="opt_nos" value="{{ $opt_count-1 }}">

                        <div class="form-group">
                            <label for="invite_people">
                                Tags
                            </label>
                            <div class="col-12" id="tag_h">
                                <input class="form-control" type="text" value="{{ implode(',', json_decode($question->tags)) }}" name="question_tags" data-role="tagsinput" id="tags">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Submit
                        </button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection