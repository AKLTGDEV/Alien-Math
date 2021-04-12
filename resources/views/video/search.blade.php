@extends('layouts.app')

@section('content')

<link href="https://vjs.zencdn.net/7.11.4/video-js.css" rel="stylesheet" />
<script src="https://vjs.zencdn.net/7.11.4/video.min.js"></script>

<div class="container mt-2">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header"><strong>{{ __($hits . " results gathered in " . $exec_time . " ms") }}</strong></div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    <?php $count = 1; ?>
                    @foreach($results as $res)

                    <div class="card mb-1">
                        <div class="row">
                            <div class="col-md-6">

                                <video style="width: 100%;" id="my-video" class="video-js" controls preload="auto" data-setup="{}">
                                    <source src="{{ route('video.stream', [$res->id]) }}" type="video/mp4" />
                                    <p class="vjs-no-js">
                                        To view this video please enable JavaScript, and consider upgrading to a
                                        web browser that
                                        <a href="https://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
                                    </p>
                                </video>

                            </div>
                            <div class="col-md-6">
                                <h4 class="text-bold mt-1">
                                    Hit #{{ $count }}
                                </h4> <br>

                                <ul>
                                    <li><span class="text-muted">"{{ $res->filename }}"</span></li>
                                    <li>Uploaded by <span class="text-muted">{{ "@".$res->uploader }}</span></li>
                                    <li>Video ID: <span class="text-muted">{{ $res->id }}</span></li>
                                    <ul>
                                        <li>{{ count(json_decode($res->MCQ)) }} MCQs attached</li>
                                        <li>{{ count(json_decode($res->SAQ)) }} SAQs attached</li>
                                        <li>{{ count(json_decode($res->SQA)) }} SQAs attached</li>
                                    </ul>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <?php $count++; ?>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection