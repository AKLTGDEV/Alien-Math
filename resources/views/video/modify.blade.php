@extends('layouts.app')

@section('content')

<div class="container-fluid mt-2">
    <div class="row">
        <div class="col-sm-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    Modify Video
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <h4 class="text-bold">Attached Questions</h4>
                        @foreach($posts as $p)
                        <li class="list-group-item">
                            {{ $p['type'] }} <b>#{{ $p['id'] }}</b>
                            <a class="btn btn-sm btn-info" href="{{ $p['url'] }}">
                                View {{ $p['type'] }}
                            </a>
                            <a class="btn btn-sm btn-danger" href="{{ $p['detach'] }}">
                                Detach
                            </a>
                        </li>
                        @endforeach

                        <h4 class="mt-2">Attach more Questions</h4>

                        <div class="card p-3">
                            <form action="{{ route('video.attach', [$video->id]) }}" method="get">
                                @csrf

                                <div class="form-group">
                                    <select class="custom-select" required name="qtype">
                                        <option value="">Question Type</option>
                                        <option value="MCQ">MCQ</option>
                                        <option value="SAQ">SAQ</option>
                                        <option value="SQA">SQA</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="qid" class="text-muted">Question ID</label>
                                    <input class="form-control" type="number" name="qid" id="qid" required>
                                </div>


                                <button type="submit" class="btn btn-md btn-primary">
                                    Attach
                                </button>
                            </form>
                        </div>

                    </ul>
                </div>
                <div class="card-footer">
                    <a href="{{ route('video.delete', [$video->id]) }}" class="btn btn-sm btn-danger">
                        Delete
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection