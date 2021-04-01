@extends('layouts.app')
@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card">
                <div class="card-header">{{ __('Upload MCQ') }}</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    <form action="{{ route('uploadpost.validate') }}" method="post" enctype="multipart/form-data">
                        @csrf

                        <div class="container-fluid">
                            <div class="form-row mb-1 text-muted text-bold">
                                <strong>
                                    Upload the CSV file below. Required Headers are:
                                    <ul>
                                        <li>Question: The Question Body</li>
                                        <li>Grade: P1->P6 / S1->S4</li>
                                        <li>Difficulty: 1/2/3</li>
                                        <li>Options: A1, A2, A3, A4</li>
                                        <li>Correct: 1/2/3/4</li>
                                        <li>Topics: Comma-seperated list of topics</li>
                                    </ul>

                                    Optional Headers:
                                    <ul>
                                        <li>Explanation: Detailed Explanation of the answer</li>
                                    </ul>
                                </strong>
                            </div>

                            <div class="custom-file mb-2">
                                <input name="csv" type="file" class="custom-file-input" id="csv_file_v" required>
                                <label class="custom-file-label" for="csv_file_v">Choose CSV file</label>
                                <div class="invalid-feedback">Example invalid custom file feedback</div>
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