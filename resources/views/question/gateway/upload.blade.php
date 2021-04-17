@extends('layouts.app')
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 offset-3">

            <div class="card mt-2">
                <div class="card-header">
                    Upload Question
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <a href="{{ route('uploadpost') }}" class="list-group-item">
                            Post
                        </a>
                        <a href="{{ route('uploadsaq') }}" class="list-group-item">
                            SAQ
                        </a>
                        <a href="{{ route('uploadsqa') }}" class="list-group-item">
                            SQA
                        </a>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection