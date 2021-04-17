@extends('layouts.app')
@section('content')

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

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

        var tags_src = JSON.parse('<?php echo json_encode($topics); ?>');
        $('#topics').tagsInput({
            autocomplete: {
                source: tags_src
            }
        });

        $("#grade").val("{{ $question->type }}");
        $("#difficulty").val("{{ $question->difficulty }}");
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

@endsection