@extends('layouts.app')

@section('content')

<div class="container-fluid mt-2">
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
                    <form action="{{ route('video.attach', [$video->id]) }}" method="get">
                        @csrf
                        <input type="text" style="display:none;" name="qtype" value="{{ $question->Table() }}">
                        <input type="text" style="display:none;" name="qid" value="{{ $question->id }}">

                        <button type="submit" class="btn btn-sm btn-primary">
                            Attach
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@endsection