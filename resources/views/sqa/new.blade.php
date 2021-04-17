@extends('layouts.app')
@section('content')

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

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

        var tags_src = JSON.parse('<?php echo json_encode($topics); ?>');

        $('#topics').tagsInput({
            autocomplete: {
                source: tags_src
            }
        });

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
    })
</script>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card mt-2">
                <div class="card-header">{{ __('Create SQA') }}</div>

                <div class="card-body">
                    @if ( count( $errors ) > 0 )
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                        @endforeach
                    </div>
                    @endif

                    <form id="f" action="{{ route('newsqasubmit') }}" method="post">
                        @csrf

                        <input id="submit_mode" name="submit_mode" type="text" style="display:none;" value="1" />
                        <!-- MODE 1: Submit and Show the Q
                             MODE 2: Submit and Post Another
                        -->

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