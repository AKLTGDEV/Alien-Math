@extends('layouts.app')

@section('content')

<style>
    html,
    body {
        height: 100%;
    }

    .global-container {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    form {
        padding-top: 10px;
        font-size: 14px;
        margin-top: 30px;
    }

    .card-title {
        font-weight: 300;
    }

    .btn {
        font-size: 14px;
    }


    .welcome-sect {
        margin-top: 20px;
    }

    .alert {
        font-size: 13px;
        margin-top: 20px;
    }
</style>

<div class="global-container container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card welcome-sect">
                <form action="{{ route('usersetup.submit') }}" method="get">
                    <div class="card-body">
                        <h3 class="card-title text-center">Welcome to {{ config('app.name', 'Crowdoubt') }} !</h3>

                        <div class="card-text text-center text-primary">
                            Select your Grade and level to Begin
                        </div>

                        @if ( count( $errors ) > 0 )
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                            @endforeach
                        </div>
                        @endif


                        @csrf

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
                            <label for="level" class="col-md-4 col-form-label text-md-right">Level</label>

                            <select class="form-control col-md-6" id="level" name="level">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>



                    </div>

                    <div class="card-footer">
                        <button class="btn btn-primary" type="submit">
                            Done
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



@endsection