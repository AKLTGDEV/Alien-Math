@extends('layouts.app')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/css/suneditor.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/suneditor.min.js"></script>

<script>
    $(document).ready(function(e) {
        const body_editor = SUNEDITOR.create((document.getElementById('body') || 'body'), {
            minWidth: "100%",
        });
        body_editor.onChange = (contents, core) => {
            body_editor.save();
        }
    })
</script>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    Report {{ $type }} #{{ $question->id }}
                </div>
                <div class="card-body">
                    <form action="{{ route('question.report.submit') }}" method="get">
                        @csrf

                        <input type="text" name="type" value="{{ $type }}" style="display: none;">
                        <input type="text" name="id" value="{{ $question->id }}" style="display: none;">

                        <div class="container-fluid">
                            <div class="form-group">
                                <textarea name="body" id="body">
                                    Descibe the problem
                                </textarea>
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