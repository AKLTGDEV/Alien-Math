@extends('layouts.app')

@section('content')


<script>
    $(document).ready(function() {
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

   
</script>


<div class="container-fluid main">
    <div class="row mt-2">
        <div class="col-12">
            @include('qb.nav')
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-12">
            <h2>
                Finalize Test
            </h2>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-12">
            <form id="f" action="{{ route('qbank_tests_finalize_submit') }}" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}

                <?php

                use Illuminate\Support\Facades\Input;
                ?>
                @if ( count( $errors ) > 0 )
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                    @endforeach
                </div>
                @endif


                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <input placeholder="Test Title" class="form-control form-input" type="text" value="{{ Input::old('title') }}" name="title" id="title" required>
                    </div>
                </div>

                <div class="row mt-2" id="q_holder">
                    <?php $i = 1; ?>
                    @foreach($qlist as $q)
                    <div class="col-sm-4">
                        <div class="card feed_item shadow">
                            <div class="card-body">
                                <p class="card-text" id="fi-body">
                                    <span class="text-bold text-secondary">Question {{ $i }}.</span>
                                    <?php echo $q['body']; ?>
                                </p>
                                @if($q['itemT'] == 'post' || $q['itemT'] == 'BANKpost')
                                <ol>
                                    @foreach($q['options'] as $o)
                                    <li>{{ $o }}</li>
                                    @endforeach
                                </ol>
                                @endif

                            </div>
                            <div class="card-footer">
                                <input placeholder="Marks" class="form-control form-input" type="number" value="{{ Input::old('marks-'.$i) }}" name="marks-{{ $i }}" id="marks-{{ $i }}" required>
                            </div>
                        </div>
                    </div>
                    <?php $i++; ?>
                    @endforeach
                </div>

                <div class="row">
                    <div class="col-md-8 offset-md-2">
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
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 offset-md-2 text-center">
                        <button type="submit" class="btn btn-md btn-outline-primary">
                            Finalize
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>


</div>


@endsection