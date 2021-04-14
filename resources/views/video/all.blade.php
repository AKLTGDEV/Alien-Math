@extends('layouts.app')

@section('content')

<div class="container-fluid mt-2">
    <div class="row">
        <div class="col-md-12">
            <div class="justify-content-between align-items-center">
                <h3 class="text-dark mb-0">All Videos</h3>

                @if(count($videos) > 0)
                <div class="row">
                    <div class="col-md-5">
                        <div class="card">
                            <div class="card-header">
                                Search Posted Videos
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <form action="{{ route('video.search') }}" method="get">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" placeholder="Enter search terms" name="q">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="submit">Search</button>
                                            </div>
                                        </div>
                                    </form>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>

    <div class="row mt-2">
        @foreach($videos as $video)
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    Video <b>#{{ $video->id }} ({{ $video->filename }})</b>
                </div>
                <div class="card-body">
                    <video id="video-{{ $video->id }}" class="video-js" controls preload="auto" style="width: 100%;" data-setup="{}">
                        <source src="{{ route('video.stream', [$video->id]) }}" type="video/mp4" />
                        <p class="vjs-no-js">
                            To view this video please enable JavaScript, and consider upgrading to a
                            web browser that
                            <a href="https://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
                        </p>
                    </video>
                </div>
                <div class="card-footer">
                    <a href="{{ route('video.modify', [$video->id]) }}" class="btn btn-sm btn-warning">
                        Modify
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@endsection