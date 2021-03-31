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
        get_subtopics("{{ $topics[0]->id }}");

        $('#topics-select').on('input', function() {
            get_subtopics($("#topics-select").val());
        });
    })

    function get_subtopics(t) {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: "{{ route('qbank_list_subtopics') }}",
            method: 'get',
            data: {
                _token: CSRF_TOKEN,
                topic: t
            },
            success: function(result) {
                $("#subtopics-holder").empty();

                if (result.status == "ok") {
                    var ST = result.st;

                    ST.forEach(sub_topic => {
                        $("#subtopics-holder").append(`<option value="${sub_topic['id']}">${sub_topic['name']}</option>`);
                    });
                }
            }
        });
    }

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
    <div class="d-sm-flex justify-content-between align-items-center py-2">
        <h3 class="text-dark">
            Add Subjective Question to Question Bank
        </h3>
    </div>

    <form id="f" action="{{ route('qbank_newq_subjective_validate') }}" method="post" enctype="multipart/form-data">
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
                <input placeholder="Question Title (not mandatory)" class="form-control form-input" type="text" value="{{ Input::old('title') }}" name="title" id="title">
            </div>
            <div class="card-body container-fluid">
                <div class="row">
                    <div class="col-12 editable-holder">
                        <textarea id="body" name="Qbody" style="width: 100%">
                        {{ Input::old('Qbody') }}
                        </textarea>
                    </div>
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
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>
                                Topic
                            </label>
                            <select class="form-control" name="topic" id="topics-select">
                                @foreach($topics as $topic)
                                <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">
                                Sub-Topic
                            </label>
                            <select id="subtopics-holder" class="form-control" name="subtopic">
                            </select>
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

@endsection